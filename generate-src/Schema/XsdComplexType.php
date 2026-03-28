<?php

namespace DMT\Ubl\Generate\Schema;

use Generator;
use SimpleXMLElement;

final readonly class XsdComplexType
{
    public string $namespace;
    public string $name;
    public ?XsdSimpleContent $simpleContent;
    /** @var array<XsdElement> */
    public array $elements;
    /** @var array<string,XsdAttribute> */
    public array $attributes;
    public bool $changesBase;

    public function __construct(
        public XsdSchema $schema,
        public SimpleXMLElement $xml,
    )
    {
        $this->namespace = $schema->namespace;
        $this->xml->registerXPathNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');

        $this->name = $this->xml->attributes()->name;
        $this->elements = iterator_to_array($this->generateElements());
        $this->simpleContent = $this->generateSimpleContent();
        $this->attributes = iterator_to_array($this->generateAttributes());
        $this->changesBase = $this->simpleContent ? $this->simpleContent->changesBase : true;
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
            yield new XsdElement($this->schema, $element);
        }
    }

    private function generateSimpleContent(): ?XsdSimpleContent
    {
        $simpleContent = $this->xml->xpath('*[local-name()="simpleContent"]')[0] ?? null;

        if (is_null($simpleContent)) {
            return null;
        }

        return new XsdSimpleContent(
            $this->schema,
            $this->schema->namespace,
            $this->schema->namespaces,
            $this->schema->version,
            $simpleContent,
        );
    }

    private function generateAttributes(): Generator
    {
        if ($this->simpleContent) {
            yield from $this->simpleContent->attributes;
        }
    }
}
