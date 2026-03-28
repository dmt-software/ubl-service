<?php

namespace DMT\Ubl\Generate\Schema;

use Generator;
use SimpleXMLElement;

final readonly class XsdExtension
{
    public string $base;
    /** @var array<string,XsdAttribute> */
    public array $attributes;
    public bool $changesBase;

    public function __construct(
        public XsdSchema $schema,
        public SimpleXMLElement $xml
    ) {
        $this->base = $this->xml->attributes()->base;
        $this->attributes = iterator_to_array($this->generateAttributes());
        $this->changesBase = count($this->xml->xpath('*[local-name()="attribute"]')) > 0;
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
        $baseType = $this->getType();

        if ($baseType instanceof XsdComplexType) {
            yield from $baseType->attributes;
        }

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