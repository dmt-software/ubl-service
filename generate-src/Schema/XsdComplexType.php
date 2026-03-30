<?php

namespace DMT\Ubl\Generate\Schema;

use Generator;
use SimpleXMLElement;

final class XsdComplexType
{
    public string $namespace;
    public string $name;
    public ?XsdSimpleContent $simpleContent;
    /** @var array<string,XsdElement> */
    public array $elements;

    public ?XsdDocumentation $documentation;

    public function __construct(
        public XsdSchema $schema,
        public SimpleXMLElement $xml,
        public ?string $version,
        public ?string $since,
        public ?string $until,
    )
    {
        $this->namespace = $schema->namespace;
        $this->xml->registerXPathNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');

        $this->name = $this->xml->attributes()->name;
        $this->elements = iterator_to_array($this->generateElements());
        $this->simpleContent = $this->generateSimpleContent();
        $this->documentation = $this->generateDocumentation();
    }

    public function __debugInfo(): array
    {
        return [
            'namespace' => $this->schema->namespace,
            'version' => $this->schema->version,
            'name' => $this->name,
        ];
    }

    /**
     * @return Generator<string,XsdElement>
     */
    private function generateElements(): Generator
    {
        foreach($this->xml->xpath('*[local-name()="sequence"]/*[local-name()="element"]') as $element) {
            $element = XsdElement::fromXml($this->schema, $element);

            yield $element->id => $element;
        }
    }

    private function generateSimpleContent(): ?XsdSimpleContent
    {
        $simpleContent = $this->xml->xpath('*[local-name()="simpleContent"]')[0] ?? null;

        if (is_null($simpleContent)) {
            return null;
        }

        return XsdSimpleContent::fromXml($this->schema, $simpleContent);
    }

    private function generateDocumentation(): ?XsdDocumentation
    {
        $component = $this->xml->xpath('*[local-name()="annotation"]/*[local-name()="documentation"]/*[local-name()="Component"]')[0] ?? null;

        if (!is_null($component)) {
            return XsdDocumentation::fromXml($this->schema, $component);
        }

        $documentation = $this->xml->xpath('*[local-name()="annotation"]/*[local-name()="documentation"]')[0] ?? null;

        if (!is_null($documentation)) {
            return XsdDocumentation::fromXml($this->schema, $documentation);
        }

        return null;
    }

    public function getElement(): ?XsdElement
    {
        foreach($this->schema->elements as $element) {
            if ($element->type == $this->name) {
                return $element;
            }
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
        foreach($this->schema->elements as $element) {
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
}
