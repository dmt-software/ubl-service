<?php

namespace DMT\Ubl\Generate\Schema;

use SimpleXMLElement;

final class XsdElement
{
    public string $id;
    public ?string $version;
    public ?string $since;
    public ?string $until;
    public ?string $ref;
    public ?string $name;
    public ?string $type;
    public string $minOccurs;
    public string $maxOccurs;

    public ?XsdDocumentation $documentation;

    private function __construct(
        public XsdSchema $schema,
    ) {
    }

    public static function fromXml(XsdSchema $schema, SimpleXMLElement $xml): XsdElement
    {
        $instance = new XsdElement($schema);

        $xml->registerXPathNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');

        $instance->name = $xml->attributes()->name ?? null;
        $instance->ref = $xml->attributes()->ref ?? null;
        $instance->type = $xml->attributes()->type ?? null;
        $instance->id = $instance->ref ?? $instance->name;
        $instance->minOccurs = $xml->attributes()->minOccurs ?? '1';
        $instance->maxOccurs = $xml->attributes()->maxOccurs ?? '1';
        $instance->version = $schema->version;
        $instance->since = $schema->version;
        $instance->until = $schema->version;

        $instance->documentation = XsdElement::generateDocumentation($schema, $xml);

        return $instance;
    }

    public function __debugInfo(): array
    {
        return [
            'namespace' => $this->schema->namespace,
            'id' => $this->id,
            'version' => $this->version,
            'since' => $this->since,
            'until' => $this->until,
            'name' => $this->name,
            'ref' => $this->ref,
            'type' => $this->type,
            'minOccurs' => $this->minOccurs,
            'maxOccurs' => $this->maxOccurs,
        ];
    }

    public function getType(): XsdComplexType|XsdSimpleType
    {
        if (!is_null($this->ref)) {
            return $this->schema->getTypeByRef($this->ref);
        }

        return $this->schema->getTypeByName($this->type);
    }

    public static function generateDocumentation(XsdSchema $schema, SimpleXMLElement $xml): ?XsdDocumentation
    {
        $component = $xml->xpath('*[local-name()="annotation"]/*[local-name()="documentation"]/*[local-name()="Component"]')[0] ?? null;

        if (!is_null($component)) {
            return XsdDocumentation::fromXml($schema, $component);
        }

        $documentation = $xml->xpath('*[local-name()="annotation"]/*[local-name()="documentation"]')[0] ?? null;

        if (!is_null($documentation)) {
            return XsdDocumentation::fromXml($schema, $documentation);
        }

        return null;
    }

    public function clone(XsdSchema $schema): XsdElement
    {
        $element = clone $this;
        $element->schema = $schema;

        return $element;
    }

    public function merge(XsdSchema $schema, XsdElement $other): XsdElement
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

        $merged = $earlier->clone($schema);
        $merged->schema = $schema;
        $merged->version = $later->version;
        $merged->since = $earlier->since ?? $earlier->version;
        $merged->until = $later->until ?? $later->version;

        if ($earlier->minOccurs == '0' || $later->minOccurs == '0') {
            $merged->minOccurs = '0';
        }

        if ($earlier->maxOccurs != '1' || $later->maxOccurs != '1') {
            $merged->maxOccurs = 'unbounded';
        }

        return $merged;
    }
}
