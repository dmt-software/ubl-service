<?php

namespace DMT\Ubl\Generate\Schema;

use FilesystemIterator;
use Generator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class XsdSchemaCollection
{
    /** @var array<string, XsdSchema> */
    public array $paths = [];

    public function __construct()
    {
    }

    public function loadSchema(string $path): XsdSchema
    {
        $path = realpath($path);

        if (!isset($this->paths[$path])) {
            $this->paths[$path] = new XsdSchema($path);
            $this->paths[$path]->init($this);
        }

        return $this->paths[$path];
    }

    public function loadSchemaDir(string $dir): void
    {
        foreach ($this->generatePaths($dir) as $path) {
            $this->loadSchema($path);
        }
    }

    /**
     * @return Generator<XsdSchema>
     */
    private function generatePaths($dir): Generator
    {
        $directory = new RecursiveDirectoryIterator(
            $dir,
            FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS
        );

        $iterator = new RecursiveIteratorIterator(
            $directory,
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if (strtolower($file->getExtension()) !== 'xsd') {
                continue;
            }

            yield $file->getPathname();
        }
    }

    /**
     * @return array<string>
     */
    public function getNamespaces(): array
    {
        $namespaces = [];

        foreach ($this->paths as $schema) {
            $namespaces[$schema->namespace] = true;
        }

        ksort($namespaces);

        return array_keys($namespaces);
    }

    public function getSchema(string $namespace, string $version = 'latest'): XsdSchema
    {
        $foundSchema = null;

        foreach ($this->paths as $schema) {
            if ($schema->namespace !== $namespace) {
                continue;
            } else {
                if (is_null($schema->version)) {
                    return $schema;
                } else {
                    if ($schema->version == $version) {
                        return $schema;
                    } else {
                        if (is_null($foundSchema)) {
                            $foundSchema = $schema;
                        } else {
                            if ($version == 'latest' && version_compare($schema->version, $foundSchema->version, '>')) {
                                $foundSchema = $schema;
                            } else {
                                if ($version == 'earliest' && version_compare(
                                        $schema->version,
                                        $foundSchema->version,
                                        '<'
                                    )) {
                                    $foundSchema = $schema;
                                }
                            }
                        }
                    }
                }
            }
        }

        if (is_null($foundSchema)) {
            throw new InvalidArgumentException("Schema with namespace $namespace @ $version not found");
        }

        return $foundSchema;
    }

    /**
     * @param string $namespace
     * @return array<XsdSchema>
     */
    public function getSchemas(string $namespace): array
    {
        return array_values(
            array_filter(
                $this->paths,
                fn(XsdSchema $schema) => $schema->namespace === $namespace
            )
        );
    }

    /**
     * @param string $namespace
     * @param bool $reverse
     * @return array<string>
     */
    public function getSchemaVersions(string $namespace, bool $reverse = false): array
    {
        $versions = [];

        foreach ($this->paths as $schema) {
            if ($schema->namespace !== $namespace) {
                continue;
            }

            $versions[] = $schema->version;
        }

        $versions = array_unique($versions);

        usort($versions, 'version_compare');

        if ($reverse) {
            $versions = array_reverse($versions);
        }

        return $versions;
    }

    /**
     * @return array<string,XsdComplexType|XsdSimpleType>
     */
    public function getTypes(string $namespace, string $operator = '>'): array
    {
        $found = [];

        foreach ($this->getSchemas($namespace) as $schema) {
            foreach ($schema->types as $id => $type) {
                if (!isset($found[$id]) || version_compare($type->version, $found[$id]->version, $operator)) {
                    $found[$id] = $type;
                }
            }
        }

        return $found;
    }

    public function getType(string $namespace, string $id, string $operator = '>'): XsdComplexType|XsdSimpleType
    {
        if ($namespace == 'http://www.w3.org/2001/XMLSchema') {
            return new XsdSimpleType(
                $namespace,
                ['xsd' => $namespace],
                null,
                $id
            );
        }

        $found = null;
        $schemas = $this->getSchemas($namespace);

        foreach ($schemas as $schema) {
            if (!isset($schema->types[$id])) {
                continue;
            }

            $type = $schema->types[$id];

            if (!$found || version_compare($type->version, $found->version, $operator)) {
                $found = $type;
            }
        }

        if ($found) {
            return $found;
        }

        return $this->getType('http://www.w3.org/2001/XMLSchema', $id);
    }

    /**
     * @return array<string,XsdElement>
     */
    public function getTypeElements(XsdComplexType $type, string $operator = '>'): array
    {
        // @todo: try to maintain element ordering across versions here

        $found = [];

        foreach ($this->getSchemas($type->namespace) as $schema) {
            if (!isset($schema->types[$type->id])) {
                continue;
            }

            $elements = $schema->types[$type->id]->elements;

            foreach ($elements as $id => $element) {
                if (!isset($found[$id]) || version_compare($element->version, $found[$id]->version, $operator)) {
                    $found[$id] = $element;
                }
            }
        }

        return $found;
    }

    public function getElement(string $namespace, string $id, string $operator = '>'): XsdElement
    {
        $found = null;

        foreach($this->getSchemas($namespace) as $schema) {
            if (!isset($schema->elements[$id])) {
                continue;
            }

            $element = $schema->elements[$id];

            if (!$found || version_compare($element->version, $found->version, $operator)) {
                $found = $element;
            }
        }

        if ($found) {
            return $found;
        }

        throw new InvalidArgumentException("cannot find element $id in $namespace");
    }

    public function getElementType(XsdElement $element): XsdComplexType|XsdSimpleType
    {
        $ns = $element->namespace;

        if (!is_null($element->type)) {
            $id = $element->type;

            if (str_contains($id, ':')) {
                [$ns, $id] = explode(':', $id);
            }

            $ns = $element->namespaces[$ns] ?? $ns;

            return $this->getType($ns, $id);
        } else {
            $id = $element->ref;

            if (str_contains($id, ':')) {
                [$ns, $id] = explode(':', $id);
            }

            $ns = $element->namespaces[$ns] ?? $ns;

            $element = $this->getElement($ns, $id);

            return $this->getElementType($element);
        }
    }
}
