<?php

namespace DMT\Ubl\Generate\Schema;

use Generator;
use SimpleXMLElement;

final class XsdExtension
{
    public string $base;
    public ?string $version = null;
    public ?string $since = null;
    public ?string $until = null;
    /** @var array<string,XsdAttribute> */
    public array $attributes;
    /**
     * @var array<string,XsdAttribute>
     */
    public array $ownAttributes;

    private function __construct(
        public XsdSchema $schema,
    ) {
    }

    public static function fromXml(XsdSchema $schema, SimpleXMLElement $xml): XsdExtension
    {
        $instance = new XsdExtension($schema);
        $instance->base = $xml->attributes()->base;
        $instance->version = $schema->version;
        $instance->since = $schema->version;
        $instance->until = $schema->version;
        $instance->ownAttributes = iterator_to_array(XsdExtension::generateOwnAttributes($schema, $xml));
        $instance->attributes = iterator_to_array(XsdExtension::generateAttributes($schema, $xml));

        return $instance;
    }

    public function __debugInfo(): array
    {
        return [
            'namespace' => $this->schema->namespace,
            'version' => $this->version,
            'since' => $this->since,
            'until' => $this->until,
            'base' => $this->base,
            'attributes' => $this->attributes,
        ];
    }

    /**
     * @return Generator<string,XsdAttribute>
     */
    public static function generateAttributes(XsdSchema $schema, SimpleXMLElement $xml): Generator
    {
        $baseType = $schema->getTypeByName($xml->attributes()->base);

        if ($baseType instanceof XsdComplexType) {
            if (isset($baseType->simpleContent->extension)) {
                yield from $baseType->simpleContent->extension->attributes;
            } else if (isset($baseType->simpleContent->restriction)) {
                yield from $baseType->simpleContent->restriction->attributes;
            }
        }

        yield from XsdExtension::generateOwnAttributes($schema, $xml);
    }

    /**
     * @return Generator<string,XsdAttribute>
     */
    public static function generateOwnAttributes(XsdSchema $schema, SimpleXMLElement $xml): Generator
    {
        foreach ($xml->xpath('*[local-name()="attribute"]') as $attribute) {
            $attribute = XsdAttribute::fromXml($schema, $attribute);

            yield $attribute->name => $attribute;
        }
    }

    public function getBaseType(): XsdComplexType|XsdSimpleType
    {
        return $this->schema->getTypeByName($this->base);
    }

    public function clone(XsdSchema $schema): XsdExtension
    {
        $extension = clone $this;
        $extension->schema = $schema;

        return $extension;
    }

    public function merge(XsdSchema $schema, XsdExtension $other): XsdExtension
    {
        if ($this->version == $other->version) {
            return $this->clone($schema);
        }

        if (version_compare($this->version, $other->version, '<')) {
            $earlier = $this;
            $later = $other;
        } else {
            $earlier = $other;
            $later = $this;
        }

        $extension = $earlier->clone($schema);
        $extension->schema = $schema;
        $extension->version = $later->version;
        $extension->since = $earlier->since ?? $earlier->version;
        $extension->until = $later->until ?? $later->version;

        $attributeNames = array_unique(array_keys(array_merge($earlier->attributes, $later->attributes)));

        foreach($attributeNames as $attributeName) {
            if(isset($earlier->attributes[$attributeName])) {
                if (isset($later->attributes[$attributeName])) {
                    $extension->attributes[$attributeName] = $earlier->attributes[$attributeName]->merge($schema, $later->attributes[$attributeName]);
                } else {
                    $extension->attributes[$attributeName] = $earlier->attributes[$attributeName]->clone($schema);
                }
            } else {
                $extension->attributes[$attributeName] = $later->attributes[$attributeName]->clone($schema);
            }
        }

        return $extension;
    }
}
