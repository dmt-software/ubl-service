<?php

namespace DMT\Ubl\Generate\Schema;

use Generator;
use SimpleXMLElement;

final readonly class XsdExtension
{
    public string $base;
    /** @var array<string,XsdAttribute> */
    public array $attributes;
    /**
     * @var array<string,XsdAttribute>
     */
    public array $ownAttributes;

    public function __construct(
        public XsdSchema $schema,
        public SimpleXMLElement $xml
    ) {
        $this->base = $this->xml->attributes()->base;
        $this->ownAttributes = iterator_to_array($this->generateOwnAttributes());
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
        $baseType = $this->getBaseType();

        if ($baseType instanceof XsdComplexType) {
            yield from $baseType->attributes;
        }

        yield from $this->ownAttributes;
    }

    /**
     * @return Generator<string,XsdAttribute>
     */
    private function generateOwnAttributes(): Generator
    {
        foreach ($this->xml->xpath('*[local-name()="attribute"]') as $attribute) {
            $attribute = new XsdAttribute(
                $this->schema,
                $attribute
            );

            yield $attribute->name => $attribute;
        }
    }

    public function getBaseType(): XsdComplexType|XsdSimpleType
    {
        return $this->schema->getTypeByName($this->base);
    }
}