<?php

namespace DMT\Ubl\Generate;

use DMT\Ubl\Generate\Schema\XsdComplexType;
use DMT\Ubl\Generate\Schema\XsdSchemaCollection;
use InvalidArgumentException;
use PhpParser\PrettyPrinter\Standard;

class CodeGenerator
{
    public function generate(string $schemaDir, string $outputDir): void
    {
        if (!is_dir($schemaDir)) {
            throw new InvalidArgumentException("Schema directory does not exist");
        }

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $schemaCollection = new XsdSchemaCollection();
        $schemaCollection->loadSchemaDir(realpath($schemaDir) . '/xsd/maindoc/');
        $builderConfig = new BuilderConfig(realpath($outputDir));

        print_r($schemaCollection->getNamespaces());

        $builder = new ClassBuilder($schemaCollection, $builderConfig);

        foreach($schemaCollection->getNamespaces() as $namespace) {
            if ($builder->isNamespaceBlacklisted($namespace)) {
                continue;
            }

            foreach($schemaCollection->getTypes($namespace) as $type) {
                if ($type instanceof XsdComplexType) {
                    $builder->saveClass($type);
                }
            }
        }
    }
}
