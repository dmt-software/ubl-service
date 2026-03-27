<?php

namespace DMT\Ubl\Generate\Schema;

use Generator;
use SimpleXMLElement;

final readonly class XsdRestriction
{
    public string $base;
    /** @var array<string,XsdAttribute> */
    public array $attributes;

    public function __construct(
        public XsdSchema $schema,
        public SimpleXMLElement $xml
    ) {
        $this->base = $this->xml->attributes()->base;
        $this->attributes = iterator_to_array($this->generateAttributes());
    }

    public function __debugInfo(): array
    {
        return [
            'namespace' => $this->schema->namespace,
            'version' => $this->schema->version,
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
                $this->schema,
                $attribute
            );

            yield $attribute->name => $attribute;
        }
    }

    public function getType(): XsdComplexType|XsdSimpleType
    {
        return $this->schema->getTypeByName($this->base);
    }
}