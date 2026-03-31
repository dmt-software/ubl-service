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
        $clone = clone $this;
        $clone->schema = $schema;

        return $clone;
    }

    public function merge(XsdSchema $schema, XsdElement $other): XsdElement
    {
        $versions = array_filter([
            $this->since, $this->version, $this->until,
            $other->since, $other->version, $other->until,
        ]);
        usort($versions, 'version_compare');

        $clone = $this->clone($schema);
        $clone->schema = $schema;
        $clone->version = null;
        $clone->since = reset($versions);
        $clone->until = end($versions);

        if ($clone->minOccurs == '0' || $other->minOccurs == '0') {
            $clone->minOccurs = '0';
        }

        if ($clone->maxOccurs != '1' || $other->maxOccurs != '1') {
            $clone->maxOccurs = 'unbounded';
        }

        return $clone;
    }
}
