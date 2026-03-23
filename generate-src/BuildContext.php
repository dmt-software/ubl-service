<?php

namespace DMT\Ubl\Generate;

use PhpParser\Builder\Class_;
use PhpParser\Builder\Namespace_;
use PhpParser\Builder\Property;
use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\Nop;
use PhpParser\PrettyPrinter\Standard;

class BuildContext
{
    public function __construct(
        public Schemas $schemas,
        public BuilderFactory $factory,
        public Namespace_ $namespace,
        public ?Class_ $class = null,
        /** @property array<Property> $properties */
        public array $properties = [],
        public array $uses = [],
        public array $namespaces = [
            '' => 'DMT\\Ubl\\Service\\Entity\\',
            'cac' => 'DMT\\Ubl\\Service\\Entity\\CommonAggregateComponents\\',
            'cbc' => 'DMT\\Ubl\\Service\\Entity\\CommonBasicComponents\\',
        ],
    ) {
    }

    public function getCode(): string
    {
        $namespaceNode = $this->namespace->getNode();
        ksort($this->uses);

        foreach (array_keys($this->uses) as $use) {
            $namespaceNode->stmts[] = $this->factory->use($use)->getNode();
        }

        $namespaceNode->stmts[] = new Nop();

        $classNode = $this->class->getNode();

        foreach($this->properties as $property) {
            $classNode->stmts[] = $property->getNode();
            $classNode->stmts[] = new Nop();
        }

        $namespaceNode->stmts[] = $classNode;

        return (new Standard())->prettyPrintFile([$namespaceNode]);
    }
}