<?php

namespace DMT\Ubl\Generate\Schema;

use Generator;
use SimpleXMLElement;

final readonly class ComplexType
{
    public string $name;
    /** @var array<string,Element> */
    public array $elements;

    public function __construct(
        private SimpleXMLElement $xml,
    )
    {
        $xml->registerXPathNamespace('xsd', 'http://www.w3.org/2001/XMLSchema');

        $this->name = $xml->attributes()->name;
        $this->elements = iterator_to_array($this->generateElements());
    }

    /**
     * @return Generator<string,Element>
     */
    private function generateElements(): Generator
    {
        foreach($this->xml->xpath('xsd:sequence/xsd:element') as $element) {
            $element = new Element($element);

            yield $element->ref ?? $element->name => $element;
        }
    }
}
