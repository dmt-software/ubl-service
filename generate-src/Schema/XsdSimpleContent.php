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
        $instance->since = $schema->version;
        $instance->until = $schema->version;
        $instance->extension = XsdSimpleContent::generateExtension($schema, $xml);
        $instance->restriction = XsdSimpleContent::generateRestriction($schema, $xml);

        return $instance;
    }

    public function __debugInfo(): array
    {
        return [
            'namespace' => $this->schema->namespace,
            'version' => $this->version,
            'since' => $this->since,
            'until' => $this->until,
            'extension' => $this->extension,
            'restriction' => $this->restriction,
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

    public function clone(XsdSchema $schema): XsdSimpleContent
    {
        $simpleContent = clone $this;
        $simpleContent->schema = $schema;

        return $simpleContent;
    }

    public function merge(XsdSchema $schema, XsdSimpleContent $other): XsdSimpleContent
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

        $simpleContent = $earlier->clone($schema);
        $simpleContent->schema = $schema;
        $simpleContent->version = $later->version;
        $simpleContent->since = $earlier->since ?? $earlier->version;
        $simpleContent->until = $later->until ?? $later->version;

        if ($simpleContent->restriction) {
            if ($later->restriction) {
                $simpleContent->restriction = $later->restriction->merge($schema, $later->restriction);
            } else {
                $simpleContent->restriction = $simpleContent->restriction->clone($schema);
            }
        }

        if ($simpleContent->extension) {
            if ($later->extension) {
                $simpleContent->extension = $later->extension->merge($schema, $later->extension);
            } else {
                $simpleContent->extension = $simpleContent->extension->clone($schema);
            }
        }

        return $simpleContent;
    }
}
