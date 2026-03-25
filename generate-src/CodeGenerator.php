<?php

namespace DMT\Ubl\Generate;

use DMT\Ubl\Generate\Schema\XsdSchemaCollection;
use PhpParser\PrettyPrinter\Standard;

class CodeGenerator
{
    public function generate(string $schemaDir, string $outputDir): void
    {
        $schemaCollection = new XsdSchemaCollection();
        $schemaCollection->loadSchemaDir($schemaDir . '/extra/');
        $schemaCollection->loadSchemaDir($schemaDir . '/xsd/maindoc/');

        $builder = new ClassBuilder($schemaCollection);
        $printer = new Standard();

        foreach($schemaCollection->getNamespaces() as $namespace) {
            foreach($schemaCollection->getTypes($namespace) as $type) {
                $stmt = $builder->createClass($type);

                echo $printer->prettyPrint([$stmt]);
            }
        }
    }
}
