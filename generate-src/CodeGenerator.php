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
//        $schemaCollection->loadSchema(realpath($schemaDir) . '/xsd/maindoc/UBL-CreditNote-2.1.xsd');
//        $schemaCollection->loadSchema(realpath($schemaDir) . '/xsd/maindoc/UBL-CreditNote-2.2.xsd');
//        $schemaCollection->loadSchema(realpath($schemaDir) . '/xsd/maindoc/UBL-CreditNote-2.3.xsd');
//        $schemaCollection->loadSchema(realpath($schemaDir) . '/xsd/maindoc/UBL-CreditNote-2.4.xsd');
        $builderConfig = new BuilderConfig(realpath($outputDir));

        $builder = new ClassBuilder($builderConfig);

        foreach($schemaCollection->getNamespaces() as $namespace) {
            if ($builder->isNamespaceBlacklisted($namespace)) {
                continue;
            }

            echo "building namespace $namespace\n";

            foreach($schemaCollection->getSchemas($namespace) as $schema) {
                echo "building schema $schema->path ($schema->version)\n";

                foreach($schema->types as $type) {
                    if (!$builder->shouldBuild($type)) {
                        // echo "skip building type $type->name\n";
                        continue;
                    }

                    echo "building type $type->name\n";

                    $builder->saveClass($type);
                }
            }
        }
    }
}
