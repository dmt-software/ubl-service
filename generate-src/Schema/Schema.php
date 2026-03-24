<?php

namespace DMT\Ubl\Generate\Schema;

use Generator;
use SimpleXMLElement;

final readonly class Schema
{
    public string $namespace;
    public ?string $version;

    /** @var array<string,string>  */
    public array $namespaces;
    /** @var array<string,Schema> */
    public array $imports;
    /** @var array<string,Schema> */
    public array $includes;
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
        $this->version = $this->xml->attributes()->version ?? null;
    }

    public function init(Environment $environment): void
    {
        $this->imports = iterator_to_array($this->generateImports($environment));
        $this->includes = iterator_to_array($this->generateIncludes($environment));
        $this->namespaces = iterator_to_array($this->generateNamespaces());
        $this->elements = iterator_to_array($this->generateElements());
        $this->types = iterator_to_array($this->generateTypes());
    }

    public function __debugInfo(): array
    {
        return [
            'path' => $this->path,
            'namespace' => $this->namespace,
            'version' => $this->version,
        ];
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
    private function generateIncludes(Environment $environment): Generator
    {
        foreach($this->xml->xpath('*[local-name()="include"]') as $include) {
            $schemaLocation = $include->attributes()->schemaLocation ?? null;

            if (!$schemaLocation) {
                continue;
            }

            $path = realpath(dirname($this->path) . '/' . $schemaLocation);

            $include = $environment->loadSchema($path);

            yield $include->namespace => $include;
        }
    }

    /**
     * @return Generator<string>
     */
    private function generateImports(Environment $environment): Generator
    {
        foreach($this->xml->xpath('*[local-name()="import"]') as $import) {
            $schemaLocation = $import->attributes()->schemaLocation ?? null;

            if (!$schemaLocation) {
                continue;
            }

            $path = realpath(dirname($this->path) . '/' . $schemaLocation);

            $import = $environment->loadSchema($path);

            yield $import->namespace => $import;
        }
    }

    /**
     * @return Generator<string,Element>
     */
    private function generateElements(): Generator
    {
        foreach($this->includes as $include) {
            yield from $include->generateElements();
        }

        foreach($this->xml->xpath('*[local-name()="element"]') as $element) {
            $element = new Element($this, $element);

            yield $element->ref ?? $element->name => $element;
        }
    }

    /**
     * @return Generator<string,ComplexType|SimpleType>
     */
    private function generateTypes(): Generator
    {
        foreach($this->includes as $include) {
            yield from $include->generateTypes();
        }

        foreach($this->xml->xpath('*[local-name()="simpleType" or local-name()="complexType"]') as $type) {
            $type = new ComplexType($this, $type);

            yield $type->name => $type;
        }
    }
}
