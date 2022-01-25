<?php

/*
 * This file is part of the Koded package.
 *
 * (c) Mihail Binev <mihail@kodeart.com>
 *
 * Please view the LICENSE distributed with this source code
 * for the full copyright and license information.
 */

namespace Koded\Stdlib\Serializer;

use DateTimeImmutable;
use DateTimeInterface;
use DOMDocument;
use DOMNode;
use InvalidArgumentException;
use Koded\Stdlib\Serializer;
use Throwable;
use function array_is_list;
use function count;
use function current;
use function error_log;
use function filter_var;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_iterable;
use function is_numeric;
use function is_object;
use function Koded\Stdlib\{json_serialize, json_unserialize};
use function key;
use function preg_match;
use function str_contains;
use function str_starts_with;
use function substr;
use function trim;

/**
 * Class XmlSerializer is heavily modified Symfony encoder (XmlEncoder).
 *
 * @see https://www.w3.org/TR/xmlschema-2/#built-in-datatypes
 */
class XmlSerializer implements Serializer
{
    /** @var string The key name for the node value */
    private string $val = '#';
    private string|null $root;

    public function __construct(?string $root, string $nodeKey = '#')
    {
        $this->root = $root;
        $nodeKey = trim($nodeKey);
        if ('@' === $nodeKey || empty($nodeKey)) {
            throw new InvalidArgumentException('Invalid node key identifier', self::E_INVALID_SERIALIZER);
        }
        $this->val = $nodeKey;
    }

    public function type(): string
    {
        return Serializer::XML;
    }

    final public function val(): string
    {
        return $this->val;
    }

    /**
     * @param iterable $data
     *
     * @return string|null XML
     */
    public function serialize(mixed $data): string|null
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = false;
        if (is_iterable($data)) {
            $root = $document->createElement($this->root);
            $document->appendChild($root);
            $document->createAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:' . $this->root);
            $this->buildXml($document, $root, $data);
        } else {
            $this->appendNode($document, $document, $data, $this->root);
        }
        return $document->saveXML();
    }

    /**
     * Unserialize a proper XML document into array, scalar value or NULL.
     *
     * @param string $xml XML
     *
     * @return mixed scalar|array|null
     */
    public function unserialize(string $xml): mixed
    {
        try {
            $document = new DOMDocument('1.0', 'UTF-8');
            $document->preserveWhiteSpace = false;
            $document->loadXML($xml);
            if ($document->documentElement->hasChildNodes()) {
                return $this->parseXml($document->documentElement);
            }
            return !$document->documentElement->getAttributeNode('xmlns:xsi')
                ? $this->parseXml($document->documentElement)
                : [];

        } catch (Throwable $e) {
            error_log(PHP_EOL . "[{$e->getLine()}]: " . $e->getMessage());
            return null;
        }
    }

    private function buildXml(
        DOMDocument $document,
        DOMNode $parent,
        iterable $data): void
    {
        foreach ($data as $key => $data) {
            $isKeyNumeric = is_numeric($key);
            if (str_starts_with($key, '@') && $name = substr($key, 1)) {
                // node attribute
                $parent->setAttribute($name, $data);
            } elseif ($this->val === $key) {
                // node value
                $parent->nodeValue = $data;
            } elseif (false === $isKeyNumeric && is_array($data)) {
                /*
                 * If the data is an associative array (with numeric keys)
                 * the structure is transformed to "item" nodes:
                 *      <item key="0">$key0</item>
                 *      <item key="1">$key1</item>
                 * by appending it to the parent node (if any)
                 */
                if (array_is_list($data)) {
                    foreach ($data as $d) {
                        $this->appendNode($document, $parent, $d, $key);
                    }
                } else {
                    $this->appendNode($document, $parent, $data, $key);
                }
            } elseif ($isKeyNumeric || false === $this->hasValidName($key)) {
                /* If the key is not a valid XML tag name,
                 * transform the key to "item" node:
                 *      <item key="$key">$value</item>
                 * by appending it to the parent node (if any)
                 */
                $this->appendNode($document, $parent, $data, 'item', $key);
            } else {
                $this->appendNode($document, $parent, $data, $key);
            }
        }
    }

    private function parseXml(DOMNode $node): mixed
    {
        $attrs = $this->parseXmlAttributes($node);
        $value = $this->parseXmlValue($node);
        if (0 === count($attrs)) {
            return $value;
        }
        if (false === is_array($value)) {
            $attrs[$this->val] = $value;
            return $this->getValueByType($attrs);
        }
        if (1 === count($value) && key($value)) {
            $attrs[key($value)] = current($value);
        }
        foreach ($value as $k => $v) {
            $attrs[$k] = $v;
        }
        return $attrs;
    }

    private function parseXmlAttributes(DOMNode $node): array
    {
        if (!$node->hasAttributes()) {
            return [];
        }
        $attrs = [];
        foreach ($node->attributes as $attr) {
            /** @var \DOMAttr $attr */
            $attrs['@' . $attr->nodeName] = $attr->nodeValue;
        }
        return $attrs;
    }

    /**
     * @param DOMNode $node
     *
     * @return array|string|null
     * @throws \Exception
     */
    private function parseXmlValue(DOMNode $node): mixed
    {
        $value = [];
        if ($node->hasChildNodes()) {
            /** @var DOMNode $child */
            $child = $node->firstChild;
            if ($child->nodeType === XML_TEXT_NODE) {
                return $child->nodeValue;
            }
            if ($child->nodeType === XML_CDATA_SECTION_NODE) {
                return $child->wholeText;
            }
            foreach ($node->childNodes as $child) {
                if ($child->nodeType === XML_COMMENT_NODE) {
                    continue;
                }
                $v = $this->parseXml($child);
                if ('item' === $child->nodeName && isset($v['@key'])) {
                    $k = $v['@key'];
                    $value[$k] = $this->getValueByType($v);
                    unset($value[$k]['@key']);
                } else {
                    $value[$child->nodeName][] = $this->getValueByType($v);
                }
            }
        }
        foreach ($value as $k => $v) {
            if (is_array($v) && 1 === count($v)) {
                $value[$k] = current($v);
            }
        }
        return $value ?: '';
    }

    /**
     * Creates an XML node in the document from the provided value
     * according to the PHP type of the value.
     *
     * @param DOMDocument $document
     * @param DOMNode $parent
     * @param mixed $data
     * @param string $name
     * @param string|null $key
     */
    private function appendNode(
        DOMDocument $document,
        DOMNode $parent,
        mixed $data,
        string $name,
        string $key = null): void
    {
        $element = $document->createElement($name);
        if (null !== $key) {
            $element->setAttribute('key', $key);
        }
        if (is_iterable($data)) {
            $this->buildXml($document, $element, $data);
        } elseif (is_bool($data)) {
            $element->setAttribute('type', 'xsd:boolean');
            $element->appendChild($document->createTextNode($data));
        } elseif (is_float($data)) {
            $element->setAttribute('type', 'xsd:float');
            $element->appendChild($document->createTextNode($data));
        } elseif (is_int($data)) {
            $element->setAttribute('type', 'xsd:integer');
            $element->appendChild($document->createTextNode($data));
        } elseif (null === $data) {
            $element->setAttribute('xsi:nil', 'true');
        } elseif ($data instanceof DateTimeInterface) {
            $element->setAttribute('type', 'xsd:dateTime');
            $element->appendChild($document->createTextNode($data->format(DateTimeInterface::ISO8601)));
        } elseif (is_object($data)) {
            $element->setAttribute('type', 'xsd:object');
            $element->appendChild($document->createCDATASection(json_serialize($data)));
        } elseif (preg_match('/[<>&\'"]/', $data) > 0) {
            $element->appendChild($document->createCDATASection($data));
        } else {
            $element->appendChild($document->createTextNode($data));
        }
        $parent->appendChild($element);
    }

    /**
     * Deserialize the XML document elements into strict PHP values
     * in regard to the XSD type defined in the XML element (if any).
     *
     * [IMPORTANT]: When deserializing an XML document into values,
     * if the XmlSerializer encounters an XML element that specifies xsi:nil="true",
     * it assigns a NULL to the corresponding element and ignores any other attributes
     *
     * @param array|string $value
     * @return mixed array|string|null
     * @throws \Exception
     */
    private function getValueByType(mixed $value): mixed
    {
        if (false === is_array($value)) {
            return $value;
        }
        /*
         * [NOTE] if "xsi:nil" is NOT 'true', ignore the xsi:nil
         * and process the rest of the attributes for this element
         */
        if (isset($value['@xsi:nil']) && $value['@xsi:nil'] == 'true') {
            unset($value['@xsi:nil']);
            return null;
        }
        if (!(isset($value['@type']) && str_starts_with($value['@type'] ?? '', 'xsd:'))) {
            return $value;
        }
        $value[$this->val] = match ($value['@type']) {
            'xsd:integer' => (int)$value[$this->val],
            'xsd:boolean' => filter_var($value[$this->val], FILTER_VALIDATE_BOOL),
            'xsd:float' => (float)$value[$this->val],
            'xsd:dateTime' => new DateTimeImmutable($value[$this->val]),
            'xsd:object' => json_unserialize($value[$this->val]),
        };
        unset($value['@type']);
        if (count($value) > 1) {
            return $value;
        }
        return $value[$this->val];
    }

    private function hasValidName(int|string $key): bool
    {
        return $key &&
            !str_contains($key, ' ') &&
            preg_match('~^[\pL_][\pL0-9._:-]*$~ui', $key);
    }
}
