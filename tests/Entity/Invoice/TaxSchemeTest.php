<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\TaxScheme;
use DMT\Ubl\Service\Entity\Invoice\Type\Id;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class TaxSchemeTest extends TestCase
{
    public function testSerialize(): void
    {
        $taxScheme = new TaxScheme();
        $taxScheme->id = new Id();
        $taxScheme->id->id = 'VAT';

        $xml = simplexml_load_string($this->getSerializer()->serialize($taxScheme, 'xml'));

        $this->assertEquals('TaxScheme', $xml->getName());
        $this->assertEquals(
            strval($taxScheme->id),
            strval($xml->xpath('*[local-name()="ID"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ID"]')[0]->getNamespaces()
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->getDocNamespaces()
        );
    }

    public function getSerializer(): Serializer
    {
        $builder = SerializerBuilder::create();

        return $builder->build();
    }
}
