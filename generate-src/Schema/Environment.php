<?php

namespace DMT\Ubl\Generate\Schema;

use FilesystemIterator;
use Generator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class Environment
{
    /** @var array<string, Schema>  */
    public array $importedPaths = [];

    /** @var array<string,array<string,Schema>>  */
    public array $namespaces = [];

    public function __construct(
        public string $path,
    ){
        foreach ($this->generateImports() as $path) {
            $this->import($path);
        }
    }

    public function import(string $path): void
    {
        $path = realpath($path);

        if (in_array($path, $this->importedPaths)) {
            return;
        }

        $this->importedPaths[] = $path;

        $schema = new Schema($path);

        $this->namespaces[$schema->namespace] ??= [];
        $this->namespaces[$schema->namespace][$schema->version] = $schema;

        uksort($this->namespaces[$schema->namespace], 'version_compare');
        ksort($this->namespaces);

        foreach ($schema->imports as $path) {
            $this->import($path);
        }
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

    public function getNamespaceMinVersion(string $namespace): ?string
    {
        $versions = array_keys($this->namespaces[$namespace]);

        return reset($versions);
    }

    public function getNamespaceMaxVersion(string $namespace): ?string
    {
        $versions = array_keys($this->namespaces[$namespace]);

        return end($versions);
    }

    /**
     * @return array<string>
     */
    public function getNamespaceTypeNames(string $namespace): array
    {
        $typeNames = [];

        foreach ($this->namespaces[$namespace] as $schema) {
            $typeNames += array_keys($schema->types);
        }

        return array_unique($typeNames);
    }

    public function getTypeMinVersion(string $namespace, string $typeName): ?string
    {
        foreach($this->namespaces[$namespace] as $version => $schema) {
            if (isset($schema->types[$typeName])) {
                return $version;
            }
        }

        return null;
    }

    public function getTypeMaxVersion(string $namespace, string $typeName): ?string
    {
        /** @var array<string,Schema> $reverseVersions */
        $reverseVersions = array_reverse($this->namespaces[$namespace]);

        foreach($reverseVersions as $version => $schema) {
            if (isset($schema->types[$typeName])) {
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
        foreach ($this->namespaces[$namespace] as $schema) {
            $elementIdList = array_flip(array_keys($schema->elements));
            $elementIdList = array_map(fn($v) => $v / count($elementIdList), $elementIdList);
            $elementIdLists[] = $elementIdList;
        }

        $combined = array_merge(...$elementIdLists);

        return array_unique(array_keys($combined));
    }

    public function getNamespaceElementMinVersion(string $namespace, string $elementId): ?string
    {
        foreach($this->namespaces[$namespace] as $version => $schema) {
            if (isset($schema->elements[$elementId])) {
                return $version;
            }
        }

        return null;
    }

    public function getNamespaceElementMaxVersion(string $namespace, string $elementId): ?string
    {
        /** @var array<string,Schema> $reverseVersions */
        $reverseVersions = array_reverse($this->namespaces[$namespace]);

        foreach($reverseVersions as $version => $schema) {
            if (isset($schema->elements[$elementId])) {
                return $version;
            }
        }

        return null;
    }

    /**
     * @return array<string>
     */
    public function getTypeElementIds(string $namespace, string $typeName): array
    {
        // trying to maintain element ordering across versions here

        $elementIdLists = [];
        foreach ($this->namespaces[$namespace] as $schema) {
            if (!isset($schema->types[$typeName])) {
                continue;
            }

            $type = $schema->types[$typeName];

            $elementIdList = array_flip(array_keys($type->elements));
            $elementIdList = array_map(fn($v) => $v / count($elementIdList), $elementIdList);
            $elementIdLists[] = $elementIdList;
        }

        $combined = array_merge(...$elementIdLists);

        return array_unique(array_keys($combined));
    }

    public function getTypeElementMinVersion(string $namespace, string $typeName, string $elementId): ?string
    {
        foreach($this->namespaces[$namespace] as $version => $schema) {
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

    public function getTypeElementMaxVersion(string $namespace, string $typeName, string $elementId): ?string
    {
        /** @var array<string,Schema> $reverseVersions */
        $reverseVersions = array_reverse($this->namespaces[$namespace]);

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
}
