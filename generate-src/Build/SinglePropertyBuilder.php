<?php

namespace DMT\Ubl\Generate\Build;

use DMT\Ubl\Generate\Schema\ComplexType;
use DMT\Ubl\Generate\Schema\Element;
use DMT\Ubl\Generate\Schema\Environment;
use DMT\Ubl\Service\Entity\Namespaces;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name;

class SinglePropertyBuilder
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
        $element = $this->element;

        $nullable = $element->minOccurs == '0';

        $prop = $factory
            ->property($helper->getPropertyName($element))
            ->makePublic()
            ->setType($helper->getUnionType($element, $nullable, $uses));

        if ($nullable) {
            $prop->setDefault(null);
        }

        $uses[SerializedName::class] = true;
        $prop->addAttribute(
            $factory->attribute(
                'SerializedName',
                [
                    'name' => $element->name
                ]
            )
        );

        $uses[Type::class] = true;
        $prop->addAttribute(
            $factory->attribute(
                'Type',
                [
                    'name' => $helper->getJMSSingleType($element, $uses)
                ]
            )
        );

        $uses[XmlElement::class] = true;
        $uses[Namespaces::class] = true;
        $prop->addAttribute(
            $factory->attribute(
                'XmlElement',
                [
                    'cdata' => false,
                    'namespace' => new Arg(
                        new ClassConstFetch(
                            new Name('Namespaces'), strtoupper('NSNSNSNSNS')
                        )
                    )
                ]
            )
        );

        return $prop->getNode();
    }
}
