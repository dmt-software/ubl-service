<?php

namespace DMT\Ubl\Generate\Schema;

use Generator;
use SimpleXMLElement;

final class XsdSimpleContent
{
    public ?string $version;
    public ?string $since;
    public ?string $until;

    public ?XsdRestriction $restriction;
    public ?XsdExtension $extension;

    private function __construct(public XsdSchema $schema)
    {
    }

    public static function fromXml(XsdSchema $schema, SimpleXMLElement $xml): XsdSimpleContent
    {
        $instance = new XsdSimpleContent($schema);
        $instance->version = $schema->version;
        $instance->since = null;
        $instance->until = null;
        $instance->extension = XsdSimpleContent::generateExtension($schema, $xml);
        $instance->restriction = XsdSimpleContent::generateRestriction($schema, $xml);

        return $instance;
    }

    public function __debugInfo(): array
    {
        return [
            'version' => $this->version,
        ];
    }

    public static function generateExtension(XsdSchema $schema, SimpleXMLElement $xml): ?XsdExtension
    {
        $extension = $xml->xpath('*[local-name()="extension"]')[0] ?? null;

        if (is_null($extension)) {
            return null;
        }

        return XsdExtension::fromXml($schema, $extension);
    }

    public static function generateRestriction(XsdSchema $schema, SimpleXMLElement $xml): ?XsdRestriction
    {
        $restriction = $xml->xpath('*[local-name()="restriction"]')[0] ?? null;

        if (is_null($restriction)) {
            return null;
        }

        return XsdRestriction::fromXml($schema, $restriction);
    }

    public function getBaseType(): XsdComplexType|XsdSimpleType
    {
        if ($this->extension) {
            return $this->extension->getBaseType();
        } else {
            return $this->restriction->getBaseType();
        }
    }

    /**
     * @return Generator<string,XsdAttribute>
     */
    private function generateAttributes(): Generator
    {
        if ($this->extension) {
            yield from $this->extension->attributes;
        } else {
            yield from $this->restriction->attributes;
        }
    }

    /**
     * @return Generator<string,XsdAttribute>
     */
    private function generateOwnAttributes(): Generator
    {
        if ($this->extension) {
            yield from $this->extension->ownAttributes;
        } else {
            yield from $this->restriction->ownAttributes;
        }
    }
}
