<?php

namespace DMT\Ubl\Generate\Schema;

use SimpleXMLElement;

final readonly class XsdSimpleContent
{
    public ?XsdRestriction $restriction;
    public ?XsdExtension $extension;

    public function __construct(
        public XsdSchema $schema,
        public string $namespace,
        public array $namespaces,
        public ?string $version,
        public SimpleXMLElement $xml
    ) {
        $extension = $xml->xpath('*[local-name()="extension"]')[0] ?? null;

        if (!is_null($extension)) {
            $this->restriction = null;
            $this->extension = new XsdExtension(
                $this->schema,
                $extension
            );
        } else {
            $this->extension = null;
            $this->restriction = new XsdRestriction(
                $this->schema,
                $xml->xpath('*[local-name()="restriction"]')[0]
            );
        }
    }

    public function __debugInfo(): array
    {
        return [
            'namespace' => $this->namespace,
            'namespaces' => $this->namespaces,
            'version' => $this->version,
        ];
    }

    public function getType(): XsdComplexType|XsdSimpleType
    {
        if (!is_null($this->extension)) {
            return $this->extension->getType();
        }

        return $this->restriction->getType();
    }
}
