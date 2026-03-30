<?php

namespace DMT\Ubl\Generate\Schema;

use Generator;
use InvalidArgumentException;
use SimpleXMLElement;

final class XsdSchema
{
    public string $namespace;
    public ?string $version;
    public ?string $since;
    public ?string $until;
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

    private function __construct(
        public XsdSchemaCollection $schemaCollection,
        public ?string $path,
    ) {
    }

    public static function fromXml(
        XsdSchemaCollection $schemaCollection,
        ?string $path,
        SimpleXMLElement $xml,
    ): XsdSchema {
        $instance = new XsdSchema($schemaCollection, $path);
        if ($path) {
            $schemaCollection->paths[realpath($path)] = $instance;
        }

        $xml->registerXPathNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');
        $instance->namespace = $xml->attributes()->targetNamespace;

        if ($path && preg_match('~(?<version>\d+\.\d+).xsd$~', $path, $m)) {
            $instance->version = $m['version'];
        } else {
            $instance->version = $xml->attributes()->version ?? null;
        }
        $instance->since = $instance->version;
        $instance->until = $instance->version;

        $instance->includes = iterator_to_array(XsdSchema::generateIncludes($instance, $xml));
        $instance->imports = iterator_to_array(XsdSchema::generateImports($instance, $xml));
        $instance->namespaces = iterator_to_array(XsdSchema::generateNamespaces($instance, $xml));
        $instance->elements = iterator_to_array(XsdSchema::generateElements($instance, $xml));
        $instance->types = iterator_to_array(XsdSchema::generateTypes($instance, $xml));

        return $instance;
    }

    public function __debugInfo(): array
    {
        return [
            'path' => $this->path,
            'namespace' => $this->namespace,
            'version' => $this->version,
            'since' => $this->since,
            'until' => $this->until,
            'elements' => $this->elements,
            'types' => $this->types,
        ];
    }

    /**
     * @return Generator<string,string>
     */
    private static function generateNamespaces(XsdSchema $schema, SimpleXMLElement $xml): Generator
    {
        $namespaces = $xml->getDocNamespaces(true);

        yield '' => $schema->namespace;

        foreach ($namespaces as $prefix => $ns) {
            yield $prefix => $ns;
        }
    }

    /**
     * @return Generator<XsdSchema>
     */
    private static function generateIncludes(XsdSchema $schema, SimpleXMLElement $xml): Generator
    {
        foreach ($xml->xpath('*[local-name()="include"]') as $include) {
            $schemaLocation = $include->attributes()->schemaLocation ?? null;
            $namespace = $include->attributes()->namespace ?? null;

            $path = null;
            if ($schema->path && $schemaLocation) {
                $path = realpath(dirname($schema->path) . '/' . $schemaLocation);
            }

            yield $schema->schemaCollection->loadSchema($path, $namespace);
        }
    }

    /**
     * @return Generator<XsdSchema>
     */
    private static function generateImports(XsdSchema $schema, SimpleXMLElement $xml): Generator
    {
        foreach ($xml->xpath('*[local-name()="import"]') as $import) {
            $schemaLocation = $import->attributes()->schemaLocation ?? null;
            $namespace = $import->attributes()->namespace ?? null;

            $path = null;
            if ($schema->path && $schemaLocation) {
                $path = realpath(dirname($schema->path) . '/' . $schemaLocation);
            }

            yield $schema->schemaCollection->loadSchema($path, $namespace);
        }
    }

    /**
     * @return Generator<string,XsdElement>
     */
    private static function generateElements(XsdSchema $schema, SimpleXMLElement $xml): Generator
    {
        foreach ($xml->xpath('*[local-name()="element"]') as $element) {
            $element = XsdElement::fromXml($schema, $element);

            yield $element->id => $element;
        }
    }

    /**
     * @return Generator<string,XsdComplexType|XsdSimpleType>
     */
    private static function generateTypes(XsdSchema $schema, SimpleXMLElement $xml): Generator
    {
        foreach ($xml->xpath('*[local-name()="simpleType" or local-name()="complexType"]') as $type) {
            $type = XsdComplexType::fromXml($schema, $type);

            yield $type->name => $type;
        }
    }

    /**
     * @param string $ns
     * @param array $stack
     * @return Generator<XsdSchema>
     */
    private function generateSchemas(string $ns, array &$stack = []): Generator
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

    public function clone(): XsdSchema
    {
        $schema = clone $this;
        $schema->includes = [];
        $imports = [];

        foreach ($this->imports as $import) {
            $imports[$import->namespace] = $schema->schemaCollection->merged[$import->namespace];
        }

        $schema->imports = array_values($imports);

        $schema->types = array_map(
            fn($type) => $type->clone($schema),
            $schema->types
        );

        $schema->elements = array_map(
            fn($element) => $element->clone($schema),
            $schema->elements
        );

        return $schema;
    }

    public function merge(XsdSchema $other): XsdSchema
    {
        if ($this->version == $other->version) {
            return $this->clone();
        }

        if (version_compare($this->version, $other->version, '<')) {
            $earlier = $this;
            $later = $other;
        } else {
            $earlier = $other;
            $later = $this;
        }

        $schema = $earlier->clone();
        $schema->namespace = $later->namespace;
        $schema->version = $later->version;
        $schema->since = $earlier->since ?? $earlier->version;
        $schema->until = $later->until ?? $later->version;
        $schema->namespaces = array_merge($earlier->namespaces, $later->namespaces);
        $schema->includes = [];

        $imports = [];
        foreach ($earlier->imports as $import) {
            $imports[$import->namespace] = $schema->schemaCollection->merged[$import->namespace];
        }

        foreach ($later->imports as $import) {
            $imports[$import->namespace] = $schema->schemaCollection->merged[$import->namespace];
        }

        $schema->imports = array_values($imports);

        foreach ($later->types as $name => $type) {
            if (isset($schema->types[$name])) {
                $schema->types[$name] = $schema->types[$name]->merge($schema, $type);
            } else {
                $schema->types[$name] = $type->clone($schema);
            }
        }

        foreach ($later->elements as $id => $element) {
            if (isset($schema->elements[$id])) {
                $schema->elements[$id] = $schema->elements[$id]->merge($schema, $element);
            } else {
                $schema->elements[$id] = $element->clone($schema);
            }
        }

        return $schema;
    }
}
