<?php

namespace DMT\Ubl\Generate\Schema;

use FilesystemIterator;
use Generator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class Environment
{
    /** @var array<string, Schema>  */
    public array $paths = [];

    /** @var array<string,array<string,Schema>>  */
    public array $schemas = [];

    public function __construct(
        public string $path,
    ){
        foreach ($this->generateImports() as $path) {
            $this->loadSchema($path);
        }
    }

    public function loadSchema(string $path): Schema
    {
        $path = realpath($path);

        if (isset($this->paths[$path])) {
            return $this->paths[$path];
        }

        $this->paths[$path] = $schema = new Schema($path);

        if (!isset($this->schemas[$schema->namespace])) {
            $this->schemas[$schema->namespace] ??= [];
            $this->schemas[$schema->namespace][$schema->version] = $schema;

            uksort($this->schemas[$schema->namespace], 'version_compare');
            ksort($this->schemas);
        }

        $schema->init($this);

        return $schema;
    }

    /**
     * @return Generator<Schema>
     */
    private function generateImports(): Generator
    {
        $directory = new RecursiveDirectoryIterator(
            $this->path,
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
        return array_keys($this->schemas);
    }

    /**
     * @param string $namespace
     * @param bool $reverse
     * @return array<string,Schema>
     */
    public function getSchemas(string $namespace, bool $reverse = false): array
    {
        if (!isset($this->schemas[$namespace])) {
            throw new InvalidArgumentException("Schema namespace '$namespace' does not exist.");
        }

        $schemas = $this->schemas[$namespace];

        if ($reverse) {
            $schemas = array_reverse($schemas);
        }

        return $schemas;
    }

    public function getSchema(string $namespace, ?string $version = null): Schema
    {
        if ($version === null || $version === 'latest') {
            $version = $this->getSchemaLatestVersion($namespace);
        } else if ($version === 'earliest') {
            $version = $this->getSchemaFirstVersion($namespace);
        }

        return $this->schemas[$namespace][$version];
    }

    public function getSchemaFirstVersion(string $namespace): ?string
    {
        return $this->getSchema($namespace, 'earliest')->version;
    }

    public function getSchemaLatestVersion(string $namespace): ?string
    {
        return $this->getSchema($namespace, 'latest')->version;
    }

    /**
     * @param string $namespace
     * @param bool $reverse
     * @return array<string>
     */
    public function getSchemaVersions(string $namespace, bool $reverse = false): array
    {
        $versions = $this->schemas[$namespace];

        if ($reverse) {
            $versions = array_reverse($versions);
        }

        return array_keys($versions);
    }

    /**
     * @return array<string,ComplexType>
     */
    public function getTypes(string $namespace): array
    {
        $types = [];

        foreach ($this->schemas[$namespace] as $schema) {
            foreach($schema->types as $typeName => $type) {
                // overrides with newer types
                $types[$typeName] = $type;
            }
        }

        return $types;
    }

    public function getType(string $namespace, string $typeName): ComplexType|SimpleType
    {
        $schemas = $this->getSchemas($namespace, true);
        $lastCheckedPath = '?';

        foreach ($schemas as $schema) {
            $lastCheckedPath = basename($schema->path);
            if (isset($schema->types[$typeName])) {
                return $schema->types[$typeName];
            }
        }

        throw new InvalidArgumentException("cannot find type $typeName in namespace $namespace ($lastCheckedPath)");
    }

    public function getTypeElement(ComplexType $type, string $elementId): Element
    {
        $versions = $this->getSchemaVersions($type->namespace, true);

        foreach ($versions as $version) {
            if (!isset($version->types[$type->name])) {
                continue;
            }

            if (!isset($version->types[$type->name]->elements[$elementId])) {
                continue;
            }

            return $version->types[$type->name]->elements[$elementId];
        }

        throw new InvalidArgumentException("cannot find element $elementId of type $type->name in namespace $type->namespace");
    }

    /**
     * @return array<string,Element>
     */
    public function getTypeElements(ComplexType $type): array
    {
        // @todo: try to maintain element ordering across versions here

        $elements = [];

        foreach ($this->schemas[$type->namespace] as $schema) {
            if (!isset($schema->types[$type->name])) {
                continue;
            }

            foreach($schema->types[$type->name]->elements as $elementId => $element) {
                $elements[$elementId] = $element;
            }
        }

        return $elements;
    }

    public function getTypeMinVersion(ComplexType $type): ?string
    {
        $namespace = $type->namespace;
        $typeName = $type->name;

        foreach($this->schemas[$namespace] as $version => $schema) {
            if (isset($schema->types[$typeName])) {
                return $version;
            }
        }

        return null;
    }

    public function getTypeMaxVersion(ComplexType $type): ?string
    {
        $namespace = $type->namespace;
        $typeName = $type->name;

        /** @var array<string,Schema> $reverseVersions */
        $reverseVersions = array_reverse($this->schemas[$namespace]);

        foreach($reverseVersions as $version => $schema) {
            if (isset($schema->types[$typeName])) {
                return $version;
            }
        }

        return null;
    }

    public function getTypeElementMinVersion(ComplexType $type, string $elementId): ?string
    {
        $namespace = $type->namespace;
        $typeName = $type->name;

        foreach($this->schemas[$namespace] as $version => $schema) {
            if (!isset($schema->types[$typeName])) {
                continue;
            }

            $type = $schema->types[$typeName];

            if (isset($type->elements[$elementId])) {
                return $version;
            }
        }

        return null;
    }

    public function getTypeElementMaxVersion(ComplexType $type, string $elementId): ?string
    {
        $namespace = $type->namespace;
        $typeName = $type->name;

        /** @var array<string,Schema> $reverseVersions */
        $reverseVersions = array_reverse($this->schemas[$namespace]);

        foreach($reverseVersions as $version => $schema) {
            if (!isset($schema->types[$typeName])) {
                continue;
            }

            $type = $schema->types[$typeName];

            if (isset($type->elements[$elementId])) {
                return $version;
            }
        }

        return null;
    }

    /**
     * @return array<string>
     */
    public function getNamespaceElementIds(string $namespace): array
    {
        // trying to maintain element ordering across versions here

        $elementIdLists = [];
        foreach ($this->schemas[$namespace] as $schema) {
            $elementIdList = array_flip(array_keys($schema->elements));
            $elementIdList = array_map(fn($v) => $v / count($elementIdList), $elementIdList);
            $elementIdLists[] = $elementIdList;
        }

        $combined = array_merge(...$elementIdLists);

        return array_unique(array_keys($combined));
    }

    public function getNamespaceElementMinVersion(string $namespace, string $elementId): ?string
    {
        foreach($this->schemas[$namespace] as $version => $schema) {
            if (isset($schema->elements[$elementId])) {
                return $version;
            }
        }

        return null;
    }

    public function getNamespaceElementMaxVersion(string $namespace, string $elementId): ?string
    {
        /** @var array<string,Schema> $reverseVersions */
        $reverseVersions = array_reverse($this->schemas[$namespace]);

        foreach($reverseVersions as $version => $schema) {
            if (isset($schema->elements[$elementId])) {
                return $version;
            }
        }

        return null;
    }
}
