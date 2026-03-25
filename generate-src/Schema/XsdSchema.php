<?php

namespace DMT\Ubl\Generate\Schema;

use Generator;
use SimpleXMLElement;

final readonly class XsdSchema
{
    public string $namespace;
    public ?string $version;

    /** @var array<string,string> */
    public array $namespaces;
    /** @var array<string,XsdSchema> */
    public array $imports;
    /** @var array<string,XsdSchema> */
    public array $includes;
    /** @var array<string,XsdElement> */
    public array $elements;
    /** @var array<string,XsdComplexType> */
    public array $types;

    public SimpleXMLElement $xml;

    public function __construct(
        public string $path,
    ) {
        $this->xml = simplexml_load_file($this->path);
        $this->xml->registerXPathNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');
        $this->namespace = $this->xml->attributes()->targetNamespace;
        $this->version = $this->xml->attributes()->version ?? null;
    }

    public function __debugInfo(): array
    {
        return [
            'path' => $this->path,
            'namespace' => $this->namespace,
            'namespaces' => $this->namespaces,
            'version' => $this->version,
            'includes' => array_keys($this->includes),
            'imports' => array_keys($this->imports),
            'elements' => array_keys($this->elements),
            'types' => array_keys($this->types),
        ];
    }

    public function init(XsdSchemaCollection $schemaCollection): void
    {
        $this->includes = iterator_to_array($this->generateIncludes($schemaCollection));
        $this->imports = iterator_to_array($this->generateImports($schemaCollection));
        $this->namespaces = iterator_to_array($this->generateNamespaces());
        $this->elements = iterator_to_array($this->generateElements());
        $this->types = iterator_to_array($this->generateTypes());
    }

    /**
     * @return Generator<string,string>
     */
    private function generateNamespaces(): Generator
    {
        $namespaces = $this->xml->getDocNamespaces(true);

        yield '' => $this->namespace;

        foreach ($namespaces as $prefix => $ns) {
            if ($this->isNamespaceUsed($prefix, $ns)) {
                yield $prefix => $ns;
            }
        }
    }

    private function isNamespaceUsed(string $prefix, string $namespace): bool
    {
        $xpaths = [];
        foreach (array_filter([$namespace, $prefix]) as $q) {
            $xpaths[] = sprintf('//*[starts-with(name(), "%s:")]', $q);
            $xpaths[] = sprintf('//*[@*[starts-with(., "%s:")]]', $q);
        }

        foreach ($xpaths as $xpath) {
            if (count($this->xml->xpath($xpath)) > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Generator<string>
     */
    private function generateIncludes(XsdSchemaCollection $schemaCollection): Generator
    {
        foreach ($this->xml->xpath('*[local-name()="include"]') as $include) {
            $schemaLocation = $include->attributes()->schemaLocation ?? null;

            if (!$schemaLocation) {
                echo "no schema location for " . $include->attributes()->namespace . "\n";
                continue;
            }

            $path = realpath(dirname($this->path) . '/' . $schemaLocation);

            $include = $schemaCollection->loadSchema($path, $this->path);

            yield $include->path => $include;
        }
    }

    /**
     * @return Generator<string>
     */
    private function generateImports(XsdSchemaCollection $schemaCollection): Generator
    {
        foreach ($this->xml->xpath('*[local-name()="import"]') as $import) {
            $schemaLocation = $import->attributes()->schemaLocation ?? null;

            if (!$schemaLocation) {
                echo "no schema location for " . $import->attributes()->namespace . "\n";
                continue;
            }

            $path = realpath(dirname($this->path) . '/' . $schemaLocation);

            $import = $schemaCollection->loadSchema($path, $this->path);

            yield $import->path => $import;
        }
    }

    /**
     * @return Generator<string,XsdElement>
     */
    private function generateElements(): Generator
    {
        foreach ($this->xml->xpath('*[local-name()="element"]') as $element) {
            $element = new XsdElement(
                $this->namespace,
                $this->namespaces,
                $this->version,
                $element
            );

            yield $element->id => $element;
        }
    }

    /**
     * @return Generator<string,XsdComplexType|XsdSimpleType>
     */
    private function generateTypes(): Generator
    {
        foreach ($this->xml->xpath('*[local-name()="simpleType" or local-name()="complexType"]') as $type) {
            $type = new XsdComplexType(
                $this->namespace,
                $this->namespaces,
                $this->version,
                $type
            );

            yield $type->id => $type;
        }
    }
}
