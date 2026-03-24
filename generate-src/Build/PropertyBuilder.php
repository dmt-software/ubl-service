<?php

namespace DMT\Ubl\Generate\Build;

use DMT\Ubl\Generate\Schema\ComplexType;
use DMT\Ubl\Generate\Schema\Element;
use DMT\Ubl\Generate\Schema\Environment;
use PhpParser\Node;

class PropertyBuilder
{
    public function __construct(
        public Environment $environment,
        public ComplexType $type,
        public Element $element,
    ) {
    }

    public function build(array &$uses): Node
    {
        if ($this->element->maxOccurs == 'unbounded') {
            $builder = new ArrayPropertyBuilder(
                $this->environment,
                $this->type,
                $this->element,
            );
        } else {
            $builder = new SinglePropertyBuilder(
                $this->environment,
                $this->type,
                $this->element,
            );
        }

        return $builder->build($uses);
    }
}
