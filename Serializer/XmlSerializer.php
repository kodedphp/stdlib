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
use Koded\Stdlib\Serializer;
use Throwable;
use function Koded\Stdlib\{json_serialize, json_unserialize};

/**
 * Class XmlSerializer is heavily modified Symfony encoder (XmlEncoder).
 *
 * @see https://www.w3.org/TR/xmlschema-2/#built-in-datatypes
 */
class XmlSerializer implements Serializer
{
    /** @var string The key name of the node value */
    private string $val = '#';

    private string|null $root;
    private DOMDocument $document;

    public function __construct(?string $root, string $nodeKey = '#')
    {
        $this->root = $root;
        $nodeKey = \trim($nodeKey);
        if ('@' === $nodeKey || empty($nodeKey)) {
            throw new \InvalidArgumentException('Invalid node key identifier', self::E_INVALID_SERIALIZER);
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
     * @return string XML
     */
    public function serialize(mixed $data): ?string
    {
        $this->document = new DOMDocument('1.0', 'UTF-8');
        $this->document->formatOutput = false;
        if (\is_iterable($data)) {
            $root = $this->document->createElement($this->root);
            $this->document->appendChild($root);
            $this->document->createAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:' . $this->root);
            $this->buildXml($root, $data);
        } else {
            $this->appendNode($this->document, $data, $this->root);
        }
        return $this->document->saveXML();
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
            return false === $document->documentElement->getAttributeNode('xmlns:xsi')
                ? $this->parseXml($document->documentElement)
                : [];

        } catch (Throwable $e) {
            \error_log(PHP_EOL . "[{$e->getLine()}]: " . $e->getMessage());
            return null;
        }
    }

    private function buildXml(DOMNode $parent, iterable $data): void
    {
        foreach ($data as $key => $data) {
            $isKeyNumeric = \is_numeric($key);
            if (0 === \strpos($key, '@') && $name = \substr($key, 1)) {
                // a node attribute
                $parent->setAttribute($name, $data);
            } elseif ($this->val === $key) {
                // the node value
                $parent->nodeValue = $data;
            } elseif (false === $isKeyNumeric && \is_array($data)) {
                if (\ctype_digit(\join('', \array_keys($data)))) {
                    foreach ($data as $d) {
                        $this->appendNode($parent, $d, $key);
                    }
                } else {
                    $this->appendNode($parent, $data, $key);
                }
            } elseif ($isKeyNumeric) {
                $this->appendNode($parent, $data, 'item', $key);
            } else {
                $this->appendNode($parent, $data, $key);
            }
        }
    }

    private function parseXml(DOMNode $node)
    {
        $attrs = $this->parseXmlAttributes($node);
        $value = $this->parseXmlValue($node);
        if (0 === \count($attrs)) {
            return $value;
        }
        if (false === \is_array($value)) {
            $attrs[$this->val] = $value;
            return $this->getValueByType($attrs);
        }
        if (1 === \count($value) && \key($value)) {
            $attrs[\key($value)] = \current($value);
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
    private function parseXmlValue(DOMNode $node)
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
            if (\is_array($v) && 1 === \count($v)) {
                $value[$k] = \current($v);
            }
        }
        return $value ?: '';
    }

    /**
     * Creates an XML node in the document from the provided value
     * according to the PHP type of the value.
     *
     * @param DOMNode     $parent
     * @param mixed       $data
     * @param string      $name
     * @param string|null $key
     */
    private function appendNode(DOMNode $parent, $data, string $name, string $key = null): void
    {
        $element = $this->document->createElement($name);
        if (null !== $key) {
            $element->setAttribute('key', $key);
        }
        if (\is_iterable($data)) {
            $this->buildXml($element, $data);
        } elseif (\is_bool($data)) {
            $element->setAttribute('type', 'xsd:boolean');
            $element->appendChild($this->document->createTextNode($data));
        } elseif (\is_float($data)) {
            $element->setAttribute('type', 'xsd:float');
            $element->appendChild($this->document->createTextNode($data));
        } elseif (\is_int($data)) {
            $element->setAttribute('type', 'xsd:integer');
            $element->appendChild($this->document->createTextNode($data));
        } elseif (null === $data) {
            $element->setAttribute('xsi:nil', 'true');
        } elseif ($data instanceof DateTimeInterface) {
            $element->setAttribute('type', 'xsd:dateTime');
            $element->appendChild($this->document->createTextNode($data->format(DateTimeImmutable::ISO8601)));
        } elseif (\is_object($data)) {
            $element->setAttribute('type', 'xsd:object');
            $element->appendChild($this->document->createCDATASection(json_serialize($data)));
        } elseif (\preg_match('/[<>&\'"]/', $data) > 0) {
            $element->appendChild($this->document->createCDATASection($data));
        } else {
            $element->appendChild($this->document->createTextNode($data));
        }
        $parent->appendChild($element);
    }

    /**
     * Deserialize the XML document elements into strict PHP values
     * in regard to the XSD type defined in the XML element (if any).
     *
     * IMPORTANT: When deserializing an XML document into values,
     * if the XmlSerializer encounters an XML element that specifies xsi:nil="true",
     * it assigns a NULL to the corresponding element and ignores any other attributes
     *
     * @param array|string $value
     * @return array|string|null
     * @throws \Exception
     */
    private function getValueByType($value)
    {
        if (false === \is_array($value)) {
            return $value;
        }
        /*
         * if "xsi:nil" is NOT 'true', ignore the xsi:nil and
         * process the rest of the attributes for this element
         */
        if (isset($value['@xsi:nil']) && $value['@xsi:nil'] == 'true') {
            unset($value['@xsi:nil']);
            return null;
        }
        if (!(isset($value['@type']) && 0 === \strpos($value['@type'] ?? '', 'xsd:', 0))) {
            return $value;
        }
        switch ($value['@type']) {
            case 'xsd:integer':
                $value[$this->val] = (int)$value[$this->val];
                break;
            case 'xsd:boolean':
                $value[$this->val] = \filter_var($value[$this->val], FILTER_VALIDATE_BOOLEAN);
                break;
            case 'xsd:float':
                $value[$this->val] = (float)$value[$this->val];
                break;
            case 'xsd:dateTime':
                if (\is_string($value[$this->val])) {
                    $value[$this->val] = new DateTimeImmutable($value[$this->val]);
                }
                break;
            case 'xsd:object':
                if (\is_string($value[$this->val])) {
                    $value[$this->val] = json_unserialize($value[$this->val]);
                }
        }
        unset($value['@type']);
        if (\count($value) > 1) {
            return $value;
        }
        return $value[$this->val];
    }
}
