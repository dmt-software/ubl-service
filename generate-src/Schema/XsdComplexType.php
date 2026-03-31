<?php

namespace DMT\Ubl\Generate\Schema;

use Generator;
use PhpParser\Node\ComplexType;
use SimpleXMLElement;

final class XsdComplexType
{
    public string $namespace;
    public string $name;
    public ?string $version;
    public ?string $since;
    public ?string $until;
    public ?XsdSimpleContent $simpleContent;
    /** @var array<string,XsdElement> */
    public array $elements;

    public ?XsdDocumentation $documentation;

    private function __construct(public XsdSchema $schema)
    {

    }

    public static function fromXml(XsdSchema $schema, SimpleXMLElement $xml): XsdComplexType
    {
        $xml->registerXPathNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');

        $instance = new XsdComplexType($schema);
        $instance->namespace = $schema->namespace;
        $instance->version = $schema->version;
        $instance->since = $schema->version;
        $instance->until = $schema->version;

        $instance->name = $xml->attributes()->name;
        $instance->elements = iterator_to_array(XsdComplexType::generateElements($schema, $xml));
        $instance->simpleContent = XsdComplexType::generateSimpleContent($schema, $xml);
        $instance->documentation = XsdComplexType::generateDocumentation($schema, $xml);

        return $instance;
    }

    public function __debugInfo(): array
    {
        return [
            'namespace' => $this->namespace,
            'name' => $this->name,
            'version' => $this->version,
            'since' => $this->since,
            'until' => $this->until,
            'simpleContent' => $this->simpleContent,
            'elements' => $this->elements,
        ];
    }

    /**
     * @return Generator<string,XsdElement>
     */
    private static function generateElements(XsdSchema $schema, SimpleXMLElement $xml): Generator
    {
        foreach ($xml->xpath('*[local-name()="sequence"]/*[local-name()="element"]') as $element) {
            $element = XsdElement::fromXml($schema, $element);

            yield $element->id => $element;
        }
    }

    private static function generateSimpleContent(XsdSchema $schema, SimpleXMLElement $xml): ?XsdSimpleContent
    {
        $simpleContent = $xml->xpath('*[local-name()="simpleContent"]')[0] ?? null;

        if (is_null($simpleContent)) {
            return null;
        }

        return XsdSimpleContent::fromXml($schema, $simpleContent);
    }

    private static function generateDocumentation(XsdSchema $schema, SimpleXMLElement $xml): ?XsdDocumentation
    {
        $component = $xml->xpath(
            '*[local-name()="annotation"]/*[local-name()="documentation"]/*[local-name()="Component"]'
        )[0] ?? null;

        if (!is_null($component)) {
            return XsdDocumentation::fromXml($schema, $component);
        }

        $documentation = $xml->xpath('*[local-name()="annotation"]/*[local-name()="documentation"]')[0] ?? null;

        if (!is_null($documentation)) {
            return XsdDocumentation::fromXml($schema, $documentation);
        }

        return null;
    }

    public function getBaseType(): XsdComplexType|XsdSimpleType
    {
        if ($this->simpleContent) {
            return $this->simpleContent->getBaseType();
        }

        return $this;
    }

    public function isRoot(): bool
    {
        // find the element that points to this type
        /** @var XsdElement $rootElement */
        $rootElement = null;
        foreach ($this->schema->elements as $element) {
            if ($element->type == $this->name) {
                $rootElement = $element;
                break;
            }
        }

        if (is_null($rootElement)) {
            return false;
        }

        return $rootElement->documentation->rootElement ?? false;
    }

    public function clone(XsdSchema $schema): XsdComplexType
    {
        $clone = clone $this;
        $clone->schema = $schema;
        $clone->namespace = $schema->namespace;
        $clone->version = $this->version;

        return $clone;
    }

    public function merge(XsdSchema $schema, XsdComplexType $other): XsdComplexType
    {
        $versions = array_filter([
            $this->since, $this->version, $this->until,
            $other->since, $other->version, $other->until,
        ]);
        usort($versions, 'version_compare');

        $clone = $this->clone($schema);
        $clone->schema = $schema;
        $clone->namespace = $schema->namespace;
        $clone->version = null;
        $clone->since = reset($versions);
        $clone->until = end($versions);

        if ($clone->simpleContent) {
            $clone->simpleContent = $clone->simpleContent->merge($schema, $other->simpleContent);
        }

        $index = 0;
        $elementIds = array_keys($clone->elements);

        foreach(array_keys($other->elements) as $otherId) {
            if (in_array($otherId, $elementIds)) {
                $index = array_search($otherId, $elementIds);
            } else {
                $elementIds = array_slice($elementIds, 0, $index) + [$otherId] + array_slice($elementIds, $index);
                $index++;
            }
        }

        foreach ($elementIds as $id) {
            if(isset($clone->elements[$id])) {
                if (isset($other->elements[$id])) {
                    $clone->elements[$id] = $clone->elements[$id]->merge($schema, $other->elements[$id]);
                } else {
                    $clone->elements[$id] = $clone->elements[$id]->clone($schema);
                }
            } else {
                $clone->elements[$id] = $other->elements[$id]->clone($schema);
            }
        }

        return $clone;
    }
}
