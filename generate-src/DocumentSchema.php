<?php

namespace DMT\Ubl\Generate;

use Generator;
use LogicException;
use PhpParser\Node\Name;
use SimpleXMLElement;

class DocumentSchema
{
    public function __construct(
        public Schemas $schemas,
        public SimpleXMLElement $document,
    ) {
    }

    public function getRootElementName(): string
    {
        return $this->document->xpath('xsd:element')[0]->attributes()['name'];
    }

    public function getClassName(): string
    {
        return $this->getRootElementName();
    }

    public function getTargetNamespace(): string
    {
        return $this->document->attributes()['targetNamespace'];
    }

    /**
     * @return Generator<ElementSchema>
     */
    public function generateElements(): Generator
    {
        $complexType = $this->document->xpath('xsd:complexType')[0] ?? null;

        if (is_null($complexType)) {
            throw new LogicException("No complex type found for {$this->getRootElementName()}");
        }

        foreach ($complexType->xpath('xsd:sequence/xsd:element') as $element) {
            $ref = (string) $element->attributes()['ref'];
            $minOccurs = (string) $element->attributes()['minOccurs'];
            $maxOccurs = (string) $element->attributes()['maxOccurs'];

            [$ns, $name] = explode(':', $ref);

            if (!isset($this->schemas->components[$ns])) {
                continue;
            }

            $component = $this->schemas->components[$ns][$name];

            $element = new ElementSchema($component, $minOccurs, $maxOccurs);

            if (!$element->isIgnored()) {
                yield $ref => $element;
            }
        }
    }

    /**
     * @return array<string,ElementSchema>
     */
    public function elements(): array
    {
        return iterator_to_array($this->generateElements());
    }

    public function build(BuildContext $ctx): void
    {
        $ctx->class = $ctx->factory->class($this->getClassName());
        $ctx->class->implement(new Name('Document'));

        foreach($this->elements() as $element) {
            $element->build($ctx);
        }
    }
}