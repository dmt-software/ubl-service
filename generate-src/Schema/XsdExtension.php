<?php

namespace DMT\Ubl\Generate\Schema;

use Generator;
use SimpleXMLElement;

final readonly class XsdExtension
{
    public string $base;
    /** @var array<string,XsdAttribute> */
    public array $attributes;

    public function __construct(
        public string $namespace,
        public array $namespaces,
        public ?string $version,
        public SimpleXMLElement $xml
    ) {
        $this->base = $this->xml->attributes()->base;
        $this->attributes = iterator_to_array($this->generateAttributes());
    }

    public function __debugInfo(): array
    {
        return [
            'namespace' => $this->namespace,
            'version' => $this->version,
            'base' => $this->base,
        ];
    }

    /**
     * @return Generator<string,XsdAttribute>
     */
    private function generateAttributes(): Generator
    {
        foreach ($this->xml->xpath('*[local-name()="attribute"]') as $attribute) {
            $attribute = new XsdAttribute(
                $this->namespace,
                $this->namespaces,
                $this->version,
                $attribute
            );

            yield $attribute->name => $attribute;
        }
    }
}