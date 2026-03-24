<?php

namespace DMT\Ubl\Generate\Schema;

use SimpleXMLElement;

final readonly class Element
{
    public Schema $schema;
    public string $namespace;
    public ?string $version;

    public ?string $ref;
    public ?string $name;
    public ?string $type;
    public ?string $minOccurs;
    public ?string $maxOccurs;

    public function __construct(
        public Schema|ComplexType $parent,
        private SimpleXMLElement $xml,
    )
    {
        $this->schema = $parent instanceof Schema ? $parent : $parent->schema;
        $this->namespace = $this->parent->namespace;
        $this->version = $this->parent->version;

        $this->xml->registerXPathNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');

        $this->name = $this->xml->attributes()->name ?? null;
        $this->ref = $this->xml->attributes()->ref ?? null;
        $this->type = $this->xml->attributes()->type ?? null;
        $this->minOccurs = $this->xml->attributes()->minOccurs ?? null;
        $this->maxOccurs = $this->xml->attributes()->maxOccurs ?? null;
    }

    public function __debugInfo(): array
    {
        return [
            'namespace' => $this->namespace,
            'version' => $this->version,
            'name' => $this->name,
            'ref' => $this->ref,
            'type' => $this->type,
        ];
    }
}
