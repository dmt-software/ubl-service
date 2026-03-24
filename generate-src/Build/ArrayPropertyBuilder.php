<?php

namespace DMT\Ubl\Generate\Build;

use DMT\Ubl\Generate\Schema\ComplexType;
use DMT\Ubl\Generate\Schema\Element;
use DMT\Ubl\Generate\Schema\Environment;
use DMT\Ubl\Service\Entity\Namespaces;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlList;
use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\PrettyPrinter\Standard;

class ArrayPropertyBuilder
{
    public function __construct(
        public Environment $environment,
        public ComplexType $type,
        public Element $element,
    ) {
    }

    public function build(array &$uses): Node
    {
        $factory = new BuilderFactory();
        $helper = new PropertyHelper($this->environment);

        $prop = $factory
            ->property($helper->getPropertyName($this->element))
            ->makePublic()
            ->setType(new Identifier('array'))
            ->setDefault([]);

        $uses[Type::class] = true;
        $prop->addAttribute(
            $factory->attribute(
                'Type',
                [
                    'name' => $helper->getJMSArrayType($this->element, $uses)
                ]
            )
        );

        $uses[XmlList::class] = true;
        $uses[Namespaces::class] = true;
        $prop->addAttribute(
            $factory->attribute(
                'XmlList',
                [
                    'entry' => $this->element->ref ?? $this->element->name,
                    'inline' => true,
                    'namespace' => new Arg(
                        new ClassConstFetch(
                            new Name('Namespaces'), strtoupper('NSNSNSNS')
                        )
                    )
                ]
            )
        );

        $prop->setDocComment(
            sprintf(
                '/** @var array<%s> $%s */',
                (new Standard())->prettyPrint([$helper->getUnionType($this->element, false, $uses)]),
                $helper->getPropertyName($this->element)
            )
        );

        return $prop->getNode();
    }
}
