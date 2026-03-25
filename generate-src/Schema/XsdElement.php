<?php

namespace DMT\Ubl\Generate\Schema;

use SimpleXMLElement;

final readonly class XsdElement
{
    public string $id;

    public ?string $ref;
    public ?string $name;
    public ?string $type;
    public ?string $minOccurs;
    public ?string $maxOccurs;

    public function __construct(
        public string $namespace,
        public array $namespaces,
        public ?string $version,
        public SimpleXMLElement $xml,
    ) {
        $this->xml->registerXPathNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');

        $this->name = $this->xml->attributes()->name ?? null;
        $this->ref = $this->xml->attributes()->ref ?? null;
        $this->id = $this->ref ?? $this->name;
        $this->type = $this->xml->attributes()->type ?? null;

        $this->minOccurs = $this->xml->attributes()->minOccurs ?? null;
        $this->maxOccurs = $this->xml->attributes()->maxOccurs ?? null;
    }

    public function __debugInfo(): array
    {
        return [
            'namespace' => $this->namespace,
            'version' => $this->version,
            'id' => $this->id,
            'name' => $this->name,
            'ref' => $this->ref,
            'type' => $this->type,
        ];
    }
}
