<?php

namespace DMT\Ubl\Generate\Schema;

use Generator;
use InvalidArgumentException;
use SimpleXMLElement;

final class XsdSchema
{
    public string $namespace;
    public ?string $version;
    /** @var array<XsdSchema> */
    public array $includes;
    /** @var array<XsdSchema> */
    public array $imports;
    /** @var array<string,XsdElement> */
    public array $elements;
    /** @var array<string,XsdComplexType|XsdSimpleType> */
    public array $types;
    /** @var array<string,string> */
    public array $namespaces;

    public function __construct(
        public XsdSchemaCollection $schemaCollection,
        public SimpleXMLElement $xml,
        public ?string $path,
    ) {
        $this->xml->registerXPathNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');
        $this->namespace = $this->xml->attributes()->targetNamespace;

        if (preg_match('~(?<version>\d+\.\d+).xsd$~', $this->path, $m)) {
            $this->version = $m['version'];
        } else {
            $this->version = $this->xml->attributes()->version ?? null;
        }
    }

    public function __debugInfo(): array
    {
        return [
            'path' => $this->path,
            'namespace' => $this->namespace,
            'version' => $this->version,
        ];
    }

    public function init(): void
    {
        $this->includes = iterator_to_array($this->generateIncludes());
        $this->imports = iterator_to_array($this->generateImports());
        $this->namespaces = iterator_to_array($this->generateNamespaces());
        $this->elements = iterator_to_array($this->generateElements());
        $this->types = iterator_to_array($this->generateTypes());
    }

    /**
     * @return Generator<string,string>
     */
    private function generateNamespaces(array &$stack = []): Generator
    {
        if (in_array($this, $stack, true)) {
            return;
        }

        $stack[] = $this;

        $namespaces = $this->xml->getDocNamespaces(true);

        yield '' => $this->namespace;

        foreach ($namespaces as $prefix => $ns) {
            yield $prefix => $ns;
        }

        foreach ($this->includes as $include) {
            yield from $include->generateNamespaces($stack);
        }
    }

    public function isNamespaceUsed(string $prefix, string $namespace): bool
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
     * @return Generator<XsdSchema>
     */
    private function generateIncludes(): Generator
    {
        foreach ($this->xml->xpath('*[local-name()="include"]') as $include) {
            $schemaLocation = $include->attributes()->schemaLocation ?? null;
            $namespace = $include->attributes()->namespace ?? null;

            $path = null;
            if ($schemaLocation) {
                $path = realpath(dirname($this->path) . '/' . $schemaLocation);
            }

            yield $this->schemaCollection->loadSchema($path, $namespace);
        }
    }

    /**
     * @return Generator<XsdSchema>
     */
    private function generateImports(): Generator
    {
        foreach ($this->xml->xpath('*[local-name()="import"]') as $import) {
            $schemaLocation = $import->attributes()->schemaLocation ?? null;
            $namespace = $import->attributes()->namespace ?? null;

            $path = null;
            if ($schemaLocation) {
                $path = realpath(dirname($this->path) . '/' . $schemaLocation);
            }

            yield $this->schemaCollection->loadSchema($path, $namespace);
        }
    }

    /**
     * @return Generator<string,XsdElement>
     */
    private function generateElements(array &$stack = []): Generator
    {
        if (in_array($this, $stack, true)) {
            return;
        }

        $stack[] = $this;

        foreach ($this->includes as $include) {
            yield from $include->generateElements($stack);
        }

        foreach ($this->xml->xpath('*[local-name()="element"]') as $element) {
            $element = XsdElement::fromXml($this, $element);

            yield $element->id => $element;
        }
    }

    /**
     * @return Generator<string,XsdComplexType|XsdSimpleType>
     */
    public function generateTypes(array &$stack = []): Generator
    {
        if (in_array($this, $stack, true)) {
            return;
        }

        $stack[] = $this;

        foreach ($this->includes as $include) {
            yield from $include->generateTypes($stack);
        }

        foreach ($this->xml->xpath('*[local-name()="simpleType" or local-name()="complexType"]') as $type) {
            $type = new XsdComplexType(
                $this,
                $type,
                $this->version,
                null,
                null,
            );

            yield $type->name => $type;
        }
    }

    /**
     * @param string $ns
     * @param array $stack
     * @return Generator<XsdSchema>
     */
    public function generateSchemas(string $ns, array &$stack = []): Generator
    {
        if (in_array($this, $stack, true)) {
            return;
        }

        $stack[] = $this;

        $ns = $this->namespaces[$ns] ?? $ns;

        if ($ns == '' || $ns == $this->namespace) {
            yield $this;
        }

        foreach ($this->includes as $include) {
            if ($ns == $include->namespace) {
                yield from $include->generateSchemas($ns, $stack);
            }
        }

        foreach ($this->imports as $import) {
            if ($ns == $import->namespace) {
                yield from $import->generateSchemas($ns, $stack);
            }
        }
    }

    public function getTypeByName(string $searchName): XsdComplexType|XsdSimpleType
    {
        $ns = '';
        $name = $searchName;
        if (str_contains($searchName, ':')) {
            [$ns, $name] = explode(':', $searchName);
        }

        foreach ($this->generateSchemas($ns) as $schema) {
            if (isset($schema->types[$name])) {
                return $schema->types[$name];
            }
        }

        if (in_array($ns, ['', 'xsd', 'http://www.w3.org/2001/XMLSchema'])) {
            return new XsdSimpleType('http://www.w3.org/2001/XMLSchema', null, $name);
        }

        throw new InvalidArgumentException("cannot find type $searchName ($ns : $name)");
    }

    public function getTypeByRef(string $searchRef): XsdComplexType|XsdSimpleType
    {
        $ns = '';
        $name = $searchRef;
        if (str_contains($searchRef, ':')) {
            [$ns, $name] = explode(':', $searchRef);
        }

        foreach ($this->generateSchemas($ns) as $schema) {
            if (isset($schema->elements[$name])) {
                return $schema->getTypeByName($schema->elements[$name]->type);
            }
        }

        throw new InvalidArgumentException("cannot find ref $searchRef ($ns : $name)");
    }
}
