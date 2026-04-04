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
//        $schemaCollection->loadSchemaDir(realpath($schemaDir) . '/xsd/maindoc/');
        $schemaCollection->loadSchema(realpath($schemaDir) . '/xsd/maindoc/UBL-CreditNote-2.1.xsd');
        $schemaCollection->loadSchema(realpath($schemaDir) . '/xsd/maindoc/UBL-Invoice-2.1.xsd');
//        $schemaCollection->loadSchema(realpath($schemaDir) . '/xsd/maindoc/UBL-CreditNote-2.2.xsd');
//        $schemaCollection->loadSchema(realpath($schemaDir) . '/xsd/maindoc/UBL-CreditNote-2.3.xsd');
//        $schemaCollection->loadSchema(realpath($schemaDir) . '/xsd/maindoc/UBL-CreditNote-2.4.xsd');

        $builderConfig = new BuilderConfig(realpath($outputDir));

        $builder = new ClassBuilder($builderConfig);

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
