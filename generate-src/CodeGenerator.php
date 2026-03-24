<?php

namespace DMT\Ubl\Generate;

use DMT\Ubl\Generate\Build\ClassBuilder;
use DMT\Ubl\Generate\Schema\Environment;
use PhpParser\PrettyPrinter\Standard;

class CodeGenerator
{
    public function generate(string $schemaDir, string $outputDir): void
    {
        $environment = new Environment($schemaDir);
        $printer = new Standard();

        foreach($environment->getNamespaces() as $namespace) {
            foreach($environment->getTypes($namespace) as $type) {
                $builder = new ClassBuilder($environment, $type);

                echo $printer->prettyPrint([$builder->build()]);
            }
        }
    }
}
