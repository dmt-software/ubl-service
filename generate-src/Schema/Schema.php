<?php

namespace DMT\Ubl\Generate\Schema;

use Generator;
use SimpleXMLElement;

final readonly class Schema
{
    public ?string $version;
    public string $namespace;
    /** @var array<string,string>  */
    public array $namespaces;
    /** @var array<string> */
    public array $imports;
    /** @var array<string,Element> */
    public array $elements;
    /** @var array<string,ComplexType> */
    public array $types;

    private SimpleXMLElement $xml;

    public function __construct(
        public string $path,
    ) {
        $this->xml = simplexml_load_file($this->path);
        $this->xml->registerXPathNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');
        $this->namespace = $this->xml->attributes()->targetNamespace;
        $this->namespaces = iterator_to_array($this->generateNamespaces());
        $this->version = $this->xml->attributes()->version ?? null;

        $this->imports = iterator_to_array($this->generateImports());
        $this->elements = iterator_to_array($this->generateElements());
        $this->types = iterator_to_array($this->generateTypes());
    }

    /**
     * @return Generator<string,string>
     */
    private function generateNamespaces(): Generator
    {
        $namespaces = $this->xml->getDocNamespaces();

        yield '' => $this->namespace;
        yield 'xsd' => 'http://www.w3.org/2001/XMLSchema';

        foreach ($namespaces as $prefix => $ns) {
            yield $prefix ?: '' => $ns;
        }
    }

    /**
     * @return Generator<string>
     */
    private function generateImports(): Generator
    {
        foreach($this->xml->xpath('xsd:import') as $import) {
            yield realpath(dirname($this->path) . '/' . $import->attributes()->schemaLocation);
        }
    }

    /**
     * @return Generator<string,Element>
     */
    private function generateElements(): Generator
    {
        foreach($this->xml->xpath('xsd:element') as $element) {
            $element = new Element($element);

            yield $element->ref ?? $element->name => $element;
        }
    }

    /**
     * @return Generator<string,ComplexType>
     */
    private function generateTypes(): Generator
    {
        foreach($this->xml->xpath('xsd:complexType') as $type) {
            $type = new ComplexType($type);

            yield $type->name => $type;
        }
    }
}
