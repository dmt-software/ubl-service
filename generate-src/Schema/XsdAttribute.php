<?php

namespace DMT\Ubl\Generate\Schema;

use SimpleXMLElement;

final readonly class XsdAttribute
{
    public string $name;
    public string $type;
    public ?string $use;

    public function __construct(
        public string $namespace,
        public array $namespaces,
        public ?string $version,
        public SimpleXMLElement $xml
    ) {
        $this->name = $this->xml->attributes()->name;
        $this->type = $this->xml->attributes()->type;
        $this->use = $this->xml->attributes()->use;
    }

    public function __debugInfo(): array
    {
        return [
            'namespace' => $this->namespace,
            'version' => $this->version,
            'name' => $this->name,
            'type' => $this->type,
            'use' => $this->use,
        ];
    }
}