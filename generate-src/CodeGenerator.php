<?php

namespace DMT\Ubl\Generate;

use DMT\Ubl\Generate\Builder\BuilderConfig;
use DMT\Ubl\Generate\Builder\ClassBuilder;
use DMT\Ubl\Generate\Schema\XsdSchemaCollection;
use InvalidArgumentException;

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

        $builder = new ClassBuilder($builderConfig);
print_r(array_keys($schemaCollection->namespaces));
        foreach($schemaCollection->namespaces as $namespace => $schema) {
            if ($builder->isNamespaceBlacklisted($namespace)) {
                continue;
            }

            echo "building $namespace\n";

            foreach ($schema->types as $type) {
                if (!$builder->shouldBuild($type)) {
                    echo "skip $namespace.$type->name\n";
                    continue;
                }

                echo "building $namespace.$type->name\n";

                $builder->saveClass($type);
            }
        }
    }
}
