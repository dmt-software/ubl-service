<?php

namespace DMT\Ubl\Generate\Schema;

use SimpleXMLElement;

final readonly class Element
{
    public ?string $ref;
    public ?string $name;
    public ?string $type;
    public ?string $minOccurs;
    public ?string $maxOccurs;

    public function __construct(
        private SimpleXMLElement $xml,
    )
    {
        $xml->registerXPathNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');

        $this->name = $xml->attributes()->name ?? null;
        $this->ref = $xml->attributes()->ref ?? null;
        $this->type = $xml->attributes()->type ?? null;
        $this->minOccurs = $xml->attributes()->minOccurs ?? null;
        $this->maxOccurs = $xml->attributes()->maxOccurs ?? null;
    }
}