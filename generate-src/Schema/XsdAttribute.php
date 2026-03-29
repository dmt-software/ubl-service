<?php

namespace DMT\Ubl\Generate\Schema;

use SimpleXMLElement;

final readonly class XsdAttribute
{
    public string $name;
    public string $type;
    public ?string $use;
    public ?XsdDocumentation $documentation;

    public function __construct(
        public XsdSchema $schema,
        public SimpleXMLElement $xml
    ) {
        $this->name = $this->xml->attributes()->name;
        $this->type = $this->xml->attributes()->type;
        $this->use = $this->xml->attributes()->use;
        $this->documentation = $this->generateDocumentation();
    }

    public function __debugInfo(): array
    {
        return [
            'namespace' => $this->schema->namespace,
            'version' => $this->schema->version,
            'name' => $this->name,
            'type' => $this->type,
            'use' => $this->use,
        ];
    }

    public function getType(): XsdComplexType|XsdSimpleType
    {
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