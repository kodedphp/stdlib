<?php

/*
 * This file is part of the Koded package.
 *
 * (c) Mihail Binev <mihail@kodeart.com>
 *
 * Please view the LICENSE distributed with this source code
 * for the full copyright and license information.
 *
 */

namespace Koded\Stdlib\Serializer;

use DateTime;
use DateTimeInterface;
use DOMDocument;
use DOMElement;
use Exception;
use Koded\Stdlib\Interfaces\Serializer;

/**
 * Class XmlSerializer is heavily copied from excellent
 * Propel 3 runtime parser (XmlParser) and modified.
 *
 */
final class XmlSerializer implements Serializer
{

    private $root;

    public function __construct(string $root)
    {
        $this->root = $root;
    }

    /**
     * @param iterable $data
     *
     * @return string XML
     */
    public function serialize($data): string
    {
        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;

        $root = $xml->createElement($this->root);
        $xml->appendChild($root);
        $this->parseFromArray($data, $root);

        return $xml->saveXML();
    }

    /**
     * @param string $document XML string
     *
     * @return array
     */
    public function unserialize($document)
    {
        $xml = new DOMDocument('1.0', 'UTF-8');

        try {
            $xml->loadXML($document);
        } catch (Exception $e) {
            return [];
        }

        return $this->parseFromElement($xml->documentElement);
    }

    public function name(): string
    {
        return Serializer::XML;
    }

    private function parseFromArray(iterable $data, DOMElement $element): DOMElement
    {
        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $key = $element->nodeName;
                if ('s' === mb_substr($key, -1, 1)) {
                    $key = mb_substr($key, 0, mb_strlen($key) - 1);
                }
            }

            try {
                $child = $element->ownerDocument->createElement($key);
            } catch (Exception $e) {
                error_log(sprintf('[%s] thrown while parsing the data into XML, with message "%s" for the key %s and value %s',
                    get_class($e),
                    $e->getMessage(),
                    var_export($key, true),
                    var_export($value, true)
                ));
                continue;
            }

            if (is_array($value)) {
                $child = $this->parseFromArray($value, $child);
            } elseif (is_string($value)) {
                $value = htmlentities($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $child->appendChild($child->ownerDocument->createCDATASection($value));
            } elseif ($value instanceof DateTimeInterface) {
                $child->setAttribute('type', 'xsd:dateTime');
                $child->appendChild($child->ownerDocument->createTextNode($value->format(DateTime::ISO8601)));
            } elseif (is_object($value)) {
                $child->setAttribute('type', 'xsd:token');
                $child->appendChild($child->ownerDocument->createCDATASection(serialize($value)));
            } else {
                $child->appendChild($child->ownerDocument->createTextNode($value));
            }

            $element->appendChild($child);
        }

        return $element;
    }

    private function parseFromElement(DOMElement $element): array
    {
        $result = [];
        $names = [];

        /** @var DOMElement $node */
        foreach ($element->childNodes as $node) {
            if (XML_TEXT_NODE === $node->nodeType) {
                continue;
            }

            $name = $node->nodeName;

            if (isset($names[$name])) {
                if (isset($result[$name])) {
                    $result[$names[$name]] = $result[$name];
                    unset($result[$name]);
                }

                $names[$name] += 1;
                $index = $names[$name];
            } else {
                $names[$name] = 0;
                $index = $name;
            }

            $hasChildNodes = $node->hasChildNodes();

            if (false === $hasChildNodes) {
                $result[$index] = null;
            } elseif ('xsd:token' === $node->getAttribute('type')) {
                $result[$index] = unserialize($node->firstChild->textContent);
            } elseif ($hasChildNodes && false === $this->hasOnlyTextNodes($node)) {
                $result[$index] = $this->parseFromElement($node);
            } elseif ($hasChildNodes && XML_CDATA_SECTION_NODE === $node->firstChild->nodeType) {
                $result[$index] = html_entity_decode($node->firstChild->textContent, ENT_QUOTES | ENT_HTML5);
            } elseif ('xsd:dateTime' === $node->getAttribute('type')) {
                $result[$index] = new DateTime($node->textContent);
            } else {
                $result[$index] = $node->textContent;
            }
        }

        return $result;
    }

    private function hasOnlyTextNodes(DOMElement $node): bool
    {
        foreach ($node->childNodes as $child) {
            if (($child->nodeType !== XML_CDATA_SECTION_NODE) && ($child->nodeType !== XML_TEXT_NODE)) {
                return false;
            }
        }

        return true;
    }
}
