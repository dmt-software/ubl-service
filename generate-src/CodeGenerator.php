<?php

namespace DMT\Ubl\Generate;

use DMT\Ubl\Generate\Schema\Environment;

class CodeGenerator
{
    public function generate(string $schemaDir, string $outputDir): void
    {
        $environment = new Environment($schemaDir);

        foreach(array_keys($environment->namespaces) as $namespace) {
            $this->generateNamespace($environment, $namespace);
        }
    }

    private function generateNamespace(Environment $environment, string $namespace): void
    {
        $phpNamespace = $namespace;
        $minVersion = $environment->getNamespaceMinVersion($namespace);
        $maxVersion = $environment->getNamespaceMaxVersion($namespace);

        echo "namespace: $namespace ($minVersion-$maxVersion)\n";

        foreach($environment->getNamespaceTypeNames($namespace) as $typeName) {
            $this->generateType($environment, $namespace, $typeName);
        }

        foreach($environment->getNamespaceElementIds($namespace) as $elementId) {
            $this->generateNamespaceElement($environment, $namespace, $elementId);
        }
    }

    private function generateType(Environment $environment, string $namespace, string $typeName): void
    {
        $minVersion = $environment->getTypeMinVersion($namespace, $typeName);
        $maxVersion = $environment->getTypeMaxVersion($namespace, $typeName);

        echo "type: $typeName ($minVersion-$maxVersion)\n";

        foreach($environment->getTypeElementIds($namespace, $typeName) as $elementId) {
            $this->generateTypeElement($environment, $namespace, $typeName, $elementId);
        }
    }

    private function generateNamespaceElement(Environment $environment, string $namespace, string $elementId): void
    {
        $minVersion = $environment->getNamespaceElementMinVersion($namespace, $elementId);
        $maxVersion = $environment->getNamespaceElementMaxVersion($namespace, $elementId);

        echo "namespace element: $elementId ($minVersion-$maxVersion)\n";
    }

    private function generateTypeElement(Environment $environment, string $namespace, string $typeName, string $elementId): void
    {
        $minVersion = $environment->getTypeElementMinVersion($namespace, $typeName, $elementId);
        $maxVersion = $environment->getTypeElementMaxVersion($namespace, $typeName, $elementId);

        echo "type element: $elementId ($minVersion-$maxVersion)\n";
    }
}
