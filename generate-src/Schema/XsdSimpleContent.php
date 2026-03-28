<?php

namespace DMT\Ubl\Generate\Schema;

use Generator;
use SimpleXMLElement;

final readonly class XsdSimpleContent
{
    public ?XsdRestriction $restriction;
    public ?XsdExtension $extension;
    public bool $changesBase;

    /**
     * @var array<string,XsdAttribute>
     */
    public array $attributes;

    public function __construct(
        public XsdSchema $schema,
        public string $namespace,
        public array $namespaces,
        public ?string $version,
        public SimpleXMLElement $xml
    ) {
        $this->extension = $this->generateExtension();
        $this->restriction = $this->generateRestriction();
        $this->attributes = iterator_to_array($this->generateAttributes());
        $this->changesBase = $this->extension ? $this->extension->changesBase : $this->restriction->changesBase;
    }

    public function __debugInfo(): array
    {
        return [
            'namespace' => $this->namespace,
            'namespaces' => $this->namespaces,
            'version' => $this->version,
        ];
    }

    private function generateExtension(): ?XsdExtension
    {
        $extension = $this->xml->xpath('*[local-name()="extension"]')[0] ?? null;

        if (is_null($extension)) {
            return null;
        }

        return new XsdExtension($this->schema, $extension);
    }

    private function generateRestriction(): ?XsdRestriction
    {
        $restriction = $this->xml->xpath('*[local-name()="restriction"]')[0] ?? null;

        if (is_null($restriction)) {
            return null;
        }

        return new XsdRestriction($this->schema, $restriction);
    }

    public function getType(): XsdComplexType|XsdSimpleType
    {
        if($this->extension) {
            return $this->extension->getType();
        } else {
            return $this->restriction->getType();
        }
    }

    /**
     * @return Generator<string,XsdAttribute>
     */
    private function generateAttributes(): Generator
    {
        if($this->extension) {
            yield from $this->extension->attributes;
        } else {
            yield from $this->restriction->attributes;
        }
    }
}
