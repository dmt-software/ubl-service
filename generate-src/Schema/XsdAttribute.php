<?php

namespace DMT\Ubl\Generate\Schema;

use SimpleXMLElement;

final class XsdAttribute
{
    public string $name;
    public string $type;
    public ?string $use;
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
        $instance->use = $xml->attributes()->use;
        $instance->version = $schema->version;
        $instance->since = null;
        $instance->until = null;
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
}
