<?php

namespace DMT\Ubl\Generate\Schema;

use Generator;
use InvalidArgumentException;
use SimpleXMLElement;

final readonly class XsdSchema
{
    public string $namespace;
    public ?string $version;

    /** @var array<string,string> */
    public array $namespaces;
    /** @var array<XsdSchema> */
    public array $imports;
    /** @var array<XsdSchema> */
    public array $includes;
    /** @var array<string,XsdElement> */
    public array $elements;
    /** @var array<string,XsdComplexType> */
    public array $types;

    public function __construct(
        public SimpleXMLElement $xml,
        public ?string $path
    ) {
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
            'elements' => array_keys($this->elements),
            'types' => array_keys($this->types),
            'includes' => $this->includes,
            'imports' => $this->imports,
        ];
    }

    public function init(XsdSchemaCollection $schemaCollection): void
    {
        $this->includes = iterator_to_array($this->generateIncludes($schemaCollection));
        $this->imports = iterator_to_array($this->generateImports($schemaCollection));
        $this->namespaces = iterator_to_array($this->generateNamespaces());
        $elements = iterator_to_array($this->generateElements());
        ksort($elements);
        $this->elements = $elements;

        $types = iterator_to_array($this->generateTypes());
        ksort($types);
        $this->types = $types;
    }

    /**
     * @return Generator<string,string>
     */
    private function generateNamespaces(array &$yielded = []): Generator
    {
        $namespaces = $this->xml->getDocNamespaces(true);

        yield '' => $this->namespace;

        foreach ($namespaces as $prefix => $ns) {
            if (isset($yielded[$prefix])) {
                continue;
            }

            yield $prefix => $ns;
            $yielded[$prefix] = true;
        }

        foreach ($this->includes as $include) {
            yield from $include->generateNamespaces($yielded);
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
            $namespace = $include->attributes()->namespace ?? null;

            $path = null;
            if ($schemaLocation) {
                $path = realpath(dirname($this->path) . '/' . $schemaLocation);
            }

            $schema = $schemaCollection->loadSchema($path, $namespace);

            yield $schema;
        }
    }

    /**
     * @return Generator<string>
     */
    private function generateImports(XsdSchemaCollection $schemaCollection): Generator
    {
        foreach ($this->xml->xpath('*[local-name()="import"]') as $import) {
            $schemaLocation = $import->attributes()->schemaLocation ?? null;
            $namespace = $import->attributes()->namespace ?? null;

            $path = null;
            if ($schemaLocation) {
                $path = realpath(dirname($this->path) . '/' . $schemaLocation);
            }

            $schema = $schemaCollection->loadSchema($path, $namespace);

            yield $schema;
        }
    }

    /**
     * @return Generator<string,XsdElement>
     */
    private function generateElements(): Generator
    {
        foreach ($this->includes as $include) {
            yield from $include->generateElements();
        }

        foreach ($this->xml->xpath('*[local-name()="element"]') as $element) {
            $element = new XsdElement($this, $element);

            yield $element->name => $element;
        }
    }

    /**
     * @return Generator<string,XsdComplexType|XsdSimpleType>
     */
    private function generateTypes(): Generator
    {
        foreach ($this->includes as $include) {
            yield from $include->generateTypes();
        }

        foreach ($this->xml->xpath('*[local-name()="simpleType" or local-name()="complexType"]') as $type) {
            $type = new XsdComplexType($this, $type);

            yield $type->name => $type;
        }
    }

    /**
     * @param string $ns
     * @param array $yielded
     * @return Generator<XsdSchema>
     */
    public function generateSchemas(string $ns, array &$yielded = []): Generator
    {
        if (in_array($this, $yielded, true)) {
            return;
        }


        $ns = $this->namespaces[$ns] ?? $ns;

        if ($ns == '' || $ns == $this->namespace) {
            yield $this;
        }

        $yielded[] = $this;

        foreach ($this->includes as $include) {
            if ($ns == $include->namespace) {
                yield from $include->generateSchemas($ns, $yielded);
            }
        }

        foreach ($this->imports as $import) {
            if ($ns == $import->namespace) {
                yield from $import->generateSchemas($ns, $yielded);
            }
        }
    }

    public function getTypeByName(string $searchName, bool $debug = false): XsdComplexType|XsdSimpleType
    {
        $ns = '';
        $name = $searchName;
        if (str_contains($searchName, ':')) {
            [$ns, $name] = explode(':', $searchName);
        }

        foreach ($this->generateSchemas($ns) as $schema) {
            if ($debug) {
                echo "check $schema->path\n";
            }

            if (isset($schema->types[$name])) {
                return $schema->types[$name];
            }
        }

        if (in_array($ns, ['', 'xsd', 'http://www.w3.org/2001/XMLSchema'])) {
            return new XsdSimpleType('http://www.w3.org/2001/XMLSchema', null, $name);
        }

        if ($debug) {
            var_dump($this);
        }

        if (!$debug) {
            return $this->getTypeByName($searchName, true);
        }

        throw new InvalidArgumentException("cannot find type $searchName ($ns : $name)");
    }

    public function getTypeByRef(string $searchRef, bool $debug = false): XsdComplexType|XsdSimpleType
    {
        $ns = '';
        $name = $searchRef;
        if (str_contains($searchRef, ':')) {
            [$ns, $name] = explode(':', $searchRef);
        }

        foreach ($this->generateSchemas($ns) as $schema) {
            if ($debug) {
                echo "check $schema->path\n";
            }

            if (isset($schema->elements[$name])) {
                return $schema->getTypeByName($schema->elements[$name]->type);
            }
        }

        if ($debug) {
            var_dump($this);
        }

        if (!$debug) {
            return $this->getTypeByRef($searchRef, true);
        }

        throw new InvalidArgumentException("cannot find ref $searchRef ($ns : $name)");
    }
}
