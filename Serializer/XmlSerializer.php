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
final class XmlSerializer implements Serializer
{
    /** @var string|null */
    private $root;

    /** @var DOMDocument */
    private $document;

    public function __construct(?string $root)
    {
        $this->root = $root;
    }

    public function type(): string
    {
        return Serializer::XML;
    }

    /**
     * @param iterable $data
     *
     * @return string XML
     */
    public function serialize($data)
    {
        try {
            $this->document = new DOMDocument;
            $this->document->formatOutput = true;

            if (is_iterable($data)) {
                $root = $this->document->createElement($this->root);
                $this->document->appendChild($root);
                $this->document->createAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:' . $this->root);
                $this->buildXml($root, $data);
            } else {
                $this->appendNode($this->document, $data, $this->root);
            }

            return $this->document->saveXML();

        } catch (Throwable $e) {
            error_log(sprintf('[XmlSerializer::serialize] Invalid data: %s', var_export($data, true)));
            return '';
        }
    }

    /**
     * Unserialize a proper XML document.
     * However, it will try to unserialize scalar values.
     *
     * @param string $xml XML
     *
     * @return array|null|scalar
     */
    public function unserialize($xml)
    {
        try {
            $document = new DOMDocument;
            $document->preserveWhiteSpace = false;

            // silence some QA tools
            $entityLoader = libxml_disable_entity_loader(true);
            $internalErrors = libxml_use_internal_errors(true);
            libxml_clear_errors();
            @$document->loadXML($xml);
            libxml_disable_entity_loader($entityLoader);
            libxml_use_internal_errors($internalErrors);

            if ($document->documentElement->hasChildNodes()) {
                $val = $this->parseXml($document->documentElement);
                return $val['#'] ?? $val;
            }

            return false === $document->documentElement->getAttributeNode('xmlns:xsi')
                ? $this->parseXml($document->documentElement)
                : [];

        } catch (Throwable $e) {
            error_log(sprintf('[XmlSerializer::unserialize] Invalid XML data: %s', var_export($xml, true)));
            return null;
        }
    }

    private function buildXml(DOMNode $parent, iterable $data)
    {
        foreach ($data as $key => $data) {
            $isKeyNumeric = is_numeric($key);
            if ($isKeyNumeric) {
                $this->appendNode($parent, $data, 'item', $key);
            } elseif (is_array($data)) {
                if (ctype_digit(join('', array_keys($data)))) {
                    foreach ($data as $i => $d) {
                        $this->appendNode($parent, $d, $key);
                    }
                } else {
                    $this->appendNode($parent, $data, $key);
                }
            } else {
                $this->appendNode($parent, $data, $key);
            }
        }
    }

    private function appendNode(DOMNode $parent, $data, string $name, string $key = null): void
    {
        $element = $this->document->createElement($name);

        if (null !== $key) {
            $element->setAttribute('key', $key);
        }

        if (is_iterable($data)) {
            $this->buildXml($element, $data);
        } elseif (is_int($data)) {
            $element->setAttribute('type', 'xsd:integer');
            $element->appendChild($this->document->createTextNode($data));
        } elseif (is_bool($data)) {
            $element->setAttribute('type', 'xsd:boolean');
            $element->appendChild($this->document->createTextNode($data));
        } elseif (is_float($data)) {
            $element->setAttribute('type', 'xsd:float');
            $element->appendChild($this->document->createTextNode($data));
        } elseif (null === $data) {
            $element->setAttribute('xsi:nil', 'true');
        } elseif ($data instanceof DateTimeInterface) {
            $element->setAttribute('type', 'xsd:dateTime');
            $element->appendChild($this->document->createTextNode($data->format(DateTimeImmutable::ISO8601)));
        } elseif (is_object($data)) {
            $element->setAttribute('type', 'xsd:object');
            $element->appendChild($this->document->createCDATASection(json_serialize($data)));
        } elseif (preg_match('/[<>&\'"]/', $data) > 0) {
            $element->appendChild($this->document->createCDATASection($data));
        } else {
            $element->appendChild($this->document->createTextNode($data));
        }

        $parent->appendChild($element);
    }

    private function parseXml(DOMNode $node)
    {
        $attrs = $this->parseXmlAttributes($node);
        $value = $this->parseXmlValue($node);

        if (0 === count($attrs)) {
            return $value;
        }

        if (false === is_array($value)) {
            $attrs['#'] = $value;
            return $this->setValueByType($attrs);
        }

        if (1 === count($value) && key($value)) {
            $attrs[key($value)] = current($value);
        }

        return $this->setValueByType($attrs);
    }

    private function parseXmlAttributes(DOMNode $node): array
    {
        if (!$node->hasAttributes()) {
            return [];
        }

        $attrs = [];

        /** @var \DOMAttr $attr */
        foreach ($node->attributes as $attr) {
            $attrs['@' . $attr->nodeName] = $attr->nodeValue;
        }

        return $attrs;
    }

    /**
     * @param DOMNode $node
     *
     * @return array|string|null
     */
    private function parseXmlValue(DOMNode $node)
    {
        $value = [];

        if ($node->hasChildNodes()) {
            /** @var DOMNode $child */
            $child = $node->firstChild;

            if ($child->nodeType === XML_COMMENT_NODE) {
                return '';
            }

            if ($child->nodeType === XML_TEXT_NODE) {
                return $child->nodeValue;
            }

            if ($child->nodeType === XML_CDATA_SECTION_NODE) {
                return $child->wholeText;
            }

            foreach ($node->childNodes as $child) {
                $val = $this->parseXml($child);

                if ('item' === $child->nodeName && isset($val['@key'])) {
                    $value[$val['@key']] = $val['#'] ?? $val;
                } elseif ($child->nodeType !== XML_COMMENT_NODE) {
                    $value[$child->nodeName][] = $val['#'] ?? $val;
                }
            }
        }

        foreach ($value as $key => $val) {
            if (is_array($val) && 1 === count($val)) {
                $value[$key] = current($val);
            }
        }

        if ($node->hasAttributes()) {
            return $value;
        }

        return $value ?: '';
    }

    /**
     * @param array|string $value
     *
     * @return array|string|null
     * @throws \Exception
     */
    private function setValueByType($value)
    {
        if (isset($value['@type'])) {
            switch ($value['@type']) {
                case 'xsd:integer':
                    $value['#'] = (int)$value['#'];
                    break;
                case 'xsd:boolean':
                    $value['#'] = (bool)$value['#'];
                    break;
                case 'xsd:float':
                    $value['#'] = (float)$value['#'];
                    break;
                case 'xsd:dateTime':
                    $value['#'] = new DateTimeImmutable($value['#']);
                    break;
                case 'xsd:object':
                    $value['#'] = json_unserialize($value['#']);
            }
        } elseif (isset($value['@xsi:nil'])) {
            $value = null;
        }

        return $value;
    }
}
