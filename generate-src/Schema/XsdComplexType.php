<?php

namespace DMT\Ubl\Generate\Schema;

use Generator;
use SimpleXMLElement;

final readonly class XsdComplexType
{
    public string $id;
    public string $name;
    public ?XsdSimpleContent $simpleContent;
    /** @var array<string,XsdElement> */
    public array $elements;

    public function __construct(
        public string $namespace,
        public array $namespaces,
        public ?string $version,
        public SimpleXMLElement $xml,
    )
    {
        $this->xml->registerXPathNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');

        $this->id = $this->xml->attributes()->name;
        $this->name = $this->xml->attributes()->name;
        $simpleContent = $this->xml->xpath('*[local-name()="simpleContent"]')[0] ?? null;

        if (!is_null($simpleContent)) {
            $this->simpleContent = new XsdSimpleContent(
                $this->namespace,
                $this->namespaces,
                $this->version,
                $simpleContent,
            );
        } else {
            $this->simpleContent = null;
        }

        $this->elements = iterator_to_array($this->generateElements());
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

    /**
     * @return Generator<string,XsdElement>
     */
    private function generateElements(): Generator
    {
        foreach($this->xml->xpath('*[local-name()="sequence"]/*[local-name()="element"]') as $element) {
            $element = new XsdElement(
                $this->namespace,
                $this->namespaces,
                $this->version,
                $element
            );

            yield $element->id => $element;
        }
    }
}
