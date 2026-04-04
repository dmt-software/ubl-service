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
    public array $schemaPaths = [];

    /** @var array<string, XsdSchema> */
    public array $namespaces = [];

    public function __construct()
    {
    }

    public function loadSchema(?string $path, ?string $namespace = null): XsdSchema
    {
        if (is_null($path)) {
            trigger_error("null path for $namespace");
            foreach ($this->schemaPaths as $path => $schema) {
                if ($schema->namespace == $namespace) {
                    trigger_error("but found at $path");
                    return $schema;
                }
            }
            throw new InvalidArgumentException("cannot load namespace $namespace");
        }

        $path = realpath($path);

        if (!isset($this->schemaPaths[$path])) {
            $xml = simplexml_load_file($path);
            $schema = XsdSchema::fromXml($this, $path, $xml);

            $existing = $this->namespaces[$schema->namespace] ?? null;
            if (is_null($existing)) {
                $this->namespaces[$schema->namespace] = $schema->clone();
            } else {
                $this->namespaces[$schema->namespace] = $existing->merge($schema);
            }
        }

        return $this->schemaPaths[$path];
    }

    public function loadSchemaDir(string $dir): void
    {
        foreach ($this->generatePaths($dir) as $path) {
            $this->loadSchema($path, null);
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
}
