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

    public XsdSchema $xsdSchema;


    public function __construct()
    {
        $xsdXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<schema targetNamespace="http://www.w3.org/2001/XMLSchema">
</schema>
XML;

        $this->xsdSchema = new XsdSchema(simplexml_load_string($xsdXml), null);
    }

    public function loadSchema(?string $path, ?string $namespace = null): XsdSchema
    {
        if (is_null($path)) {
            trigger_error("null path for $namespace");
            foreach ($this->paths as $path => $schema) {
                if ($schema->namespace == $namespace) {
                    trigger_error("but found at $path");
                    return $schema;
                }
            }
            throw new InvalidArgumentException("cannot load namespace $namespace");
        }

        $path = realpath($path);

        if (!isset($this->paths[$path])) {
            $xml = simplexml_load_file($path);
            $this->paths[$path] = new XsdSchema($xml, $path);
            $this->paths[$path]->init($this);
        }

        return $this->paths[$path];
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
}
