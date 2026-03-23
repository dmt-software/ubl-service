<?php

namespace DMT\Ubl\Generate;

use PhpParser\BuilderFactory;

class CodeGenerator
{
    public function generate(string $workdir): void
    {
        $schemas = new Schemas(__DIR__ . '/../schema/UBL-2.1.zip', '2.1');
        $factory = new BuilderFactory();

        foreach ($schemas->documents as $name => $documentSchema) {
            $ctx = new BuildContext(
                schemas: $schemas,
                factory: $factory,
                namespace: $factory->namespace('Dmt\\Ubl\\Service\\Entity'),
            );

            $documentSchema->build($ctx);

            echo $ctx->getCode();
        }
    }
}