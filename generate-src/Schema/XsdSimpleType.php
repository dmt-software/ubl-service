<?php

namespace DMT\Ubl\Generate\Schema;

final readonly class XsdSimpleType
{
    public string $id;

    public function __construct(
        public string $namespace,
        public array $namespaces,
        public ?string $version,
        public string $name
    )
    {
        $this->id = $this->name;
    }

    public function __debugInfo(): array
    {
        return [
            'namespace' => $this->namespace,
            'version' => $this->version,
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
