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

    public function clone(XsdSchema $schema): XsdSimpleType
    {
        return clone $this;
    }

    public function merge(XsdSchema $schema, XsdSimpleType $other): XsdSimpleType
    {
        return $this->clone($schema);
    }
}
