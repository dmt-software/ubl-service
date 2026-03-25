<?php

namespace DMT\Ubl\Generate\Schema;

use SimpleXMLElement;

final readonly class XsdSimpleContent
{
    public ?XsdRestriction $restriction;
    public ?XsdExtension $extension;

    public function __construct(
        public string $namespace,
        public array $namespaces,
        public ?string $version,
        public SimpleXMLElement $xml
    )
    {
        $extension = $xml->xpath('*[local-name()="extension"]')[0] ?? null;

        if (!is_null($extension)) {
            $this->restriction = null;
            $this->extension = new XsdExtension(
                $this->namespace,
                $this->namespaces,
                $this->version,
                $extension
            );
        } else {
            $this->extension = null;
            $this->restriction = new XsdRestriction(
                $this->namespace,
                $this->namespaces,
                $this->version,
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
}
