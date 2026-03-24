<?php

namespace DMT\Ubl\Generate\Schema;

use Generator;
use SimpleXMLElement;

final readonly class ComplexType
{
    public string $namespace;
    public ?string $version;

    public string $name;
    /** @var array<string,Element> */
    public array $elements;

    public function __construct(
        public Schema $schema,
        private SimpleXMLElement $xml,
    )
    {
        $this->namespace = $this->schema->namespace;
        $this->version = $this->schema->version;

        $this->xml->registerXPathNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');

        $this->name = $this->xml->attributes()->name;
        $this->elements = iterator_to_array($this->generateElements());
    }

    public function __debugInfo(): array
    {
        return [
            'namespace' => $this->namespace,
            'version' => $this->version,
            'name' => $this->name,
        ];
    }

    /**
     * @return Generator<string,Element>
     */
    private function generateElements(): Generator
    {
        foreach($this->xml->xpath('*[local-name()="sequence"]/*[local-name()="element"]') as $element) {
            $element = new Element($this, $element);

            yield $element->ref ?? $element->name => $element;
        }
    }

    public function getElement(string $elementId): ?Element
    {
        return $this->elements[$elementId] ?? null;
    }
}
