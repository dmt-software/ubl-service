<?php

namespace DMT\Ubl\Generate\Schema;

use SimpleXMLElement;

final readonly class XsdElement
{
    public ?string $ref;
    public ?string $name;
    public ?string $type;
    public string $minOccurs;
    public string $maxOccurs;

    public ?XsdDocumentation $documentation;

    public function __construct(
        public XsdSchema $schema,
        public SimpleXMLElement $xml,
    ) {
        $this->xml->registerXPathNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');

        $this->name = $this->xml->attributes()->name ?? null;
        $this->ref = $this->xml->attributes()->ref ?? null;
        $this->type = $this->xml->attributes()->type ?? null;

        $this->minOccurs = $this->xml->attributes()->minOccurs ?? '1';
        $this->maxOccurs = $this->xml->attributes()->maxOccurs ?? '1';

        $this->documentation = $this->generateDocumentation();
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

    private function generateDocumentation(): ?XsdDocumentation
    {
        $component = $this->xml->xpath('*[local-name()="annotation"]/*[local-name()="documentation"]/*[local-name()="Component"]')[0] ?? null;

        if (!is_null($component)) {
            return new XsdDocumentation($this->schema, $component);
        }

        $documentation = $this->xml->xpath('*[local-name()="annotation"]/*[local-name()="documentation"]')[0] ?? null;

        if (!is_null($documentation)) {
            return new XsdDocumentation($this->schema, $documentation);
        }

        return null;
    }
}
