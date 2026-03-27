<?php

namespace DMT\Ubl\Generate\Schema;

final readonly class XsdSimpleType
{
    public function __construct(
        public string $namespace,
        public ?string $version,
        public string $name
    )
    {
    }

    public function __debugInfo(): array
    {
        return [
            'namespace' => $this->namespace,
            'version' => $this->version,
            'name' => $this->name,
        ];
    }
}
