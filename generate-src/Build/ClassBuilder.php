<?php

namespace DMT\Ubl\Generate\Build;

use DMT\Ubl\Generate\Schema\ComplexType;
use DMT\Ubl\Generate\Schema\Environment;
use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Stmt\Nop;

class ClassBuilder
{
    public function __construct(
        public Environment $environment,
        public ComplexType $type,
    ) {
    }

    public function build(): Node
    {
        $factory = new BuilderFactory();
        $uses = [];

        $class = $factory->class(
            $this->getClassName()
        );

        $comment = <<<COMMENT
/**
 * schema: %s
 * namespace: %s
 * version: %s
 */
COMMENT;

        $class->setDocComment(
            sprintf(
                $comment,
                $this->type->schema->path,
                $this->type->namespace,
                $this->type->version
            )
        );

        $propertyNodes = [];
        foreach($this->environment->getTypeElements($this->type) as $element) {
            $propertyBuilder = new PropertyBuilder(
                $this->environment,
                $this->type,
                $element
            );

            $propertyNodes[] = $propertyBuilder->build($uses);
        }

        $namespace = $factory->namespace($this->type->namespace);
        $namespaceNode = $namespace->getNode();
        ksort($uses);

        foreach (array_keys($uses) as $use) {
            $namespaceNode->stmts[] = $factory->use($use)->getNode();
        }

        $classNode = $class->getNode();

        foreach ($propertyNodes as $propertyNode) {
            $classNode->stmts[] = $propertyNode;
            $classNode->stmts[] = new Nop();
        }

        $namespaceNode->stmts[] = $classNode;

        return $namespaceNode;
    }

    public function getClassName(): string
    {
        return preg_replace('~Type$~', '', $this->type->name);
    }
}
