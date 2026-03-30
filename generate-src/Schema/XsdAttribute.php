<?php

namespace DMT\Ubl\Generate\Schema;

use SimpleXMLElement;

final class XsdAttribute
{
    public string $name;
    public string $type;
    public string $use;
    public ?string $version;
    public ?string $since;
    public ?string $until;
    public ?XsdDocumentation $documentation;

    private function __construct(public XsdSchema $schema)
    {
    }

    public static function fromXml(XsdSchema $schema, SimpleXMLElement $xml): XsdAttribute
    {
        $instance = new XsdAttribute($schema);

        $instance->name = $xml->attributes()->name;
        $instance->type = $xml->attributes()->type;
        $instance->use = $xml->attributes()->use ?? 'optional';
        $instance->version = $schema->version;
        $instance->since = $schema->version;
        $instance->until = $schema->version;
        $instance->documentation = XsdAttribute::generateDocumentation($schema, $xml);

        return $instance;
    }

    public function __debugInfo(): array
    {
        return [
            'namespace' => $this->schema->namespace,
            'version' => $this->schema->version,
            'name' => $this->name,
            'type' => $this->type,
            'use' => $this->use,
        ];
    }

    public function getType(): XsdComplexType|XsdSimpleType
    {
        return $this->schema->getTypeByName($this->type);
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

    public function clone(XsdSchema $schema): XsdAttribute
    {
        $attribute = clone $this;
        $attribute->schema = $schema;

        return $attribute;
    }

    public function merge(XsdSchema $schema, XsdAttribute $other): XsdAttribute
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

        $attribute = $earlier->clone($schema);
        $attribute->schema = $schema;
        $attribute->version = $later->version;
        $attribute->since = $earlier->since ?? $earlier->version;
        $attribute->until = $later->until ?? $later->version;

        if ($earlier->use == 'optional' || $later->use == 'optional') {
            $attribute->use = 'optional';
        }

        return $attribute;
    }
}
