<?php

namespace DMT\Ubl\Generate\Schema;

use SimpleXMLElement;

final class XsdElement
{
    public string $id;
    public ?string $ref;
    public ?string $name;
    public ?string $type;
    public string $minOccurs;
    public string $maxOccurs;
    public ?string $version;
    public ?string $since;
    public ?string $until;
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
        $instance->since = null;
        $instance->until = null;

        $instance->documentation = XsdElement::generateDocumentation($schema, $xml);

        return $instance;
    }

    public function __debugInfo(): array
    {
        return [
            'namespace' => $this->schema->namespace,
            'version' => $this->schema->version,
            'name' => $this->name,
            'ref' => $this->ref,
            'type' => $this->type,
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
}
