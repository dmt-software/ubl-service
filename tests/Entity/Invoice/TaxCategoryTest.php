<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\TaxCategory;
use DMT\Ubl\Service\Entity\Invoice\TaxScheme;
use DMT\Ubl\Service\Entity\Invoice\Type\Id;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class TaxCategoryTest extends TestCase
{
    public function testSerialize(): void
    {
        $taxCategory = new TaxCategory();
        $taxCategory->id = new Id();
        $taxCategory->id->id = 'S';
        $taxCategory->percent = 21;
        $taxCategory->taxScheme = new TaxScheme();
        $taxCategory->taxScheme->id = new Id();
        $taxCategory->taxScheme->id->id = 'VAT';

        $xml = simplexml_load_string($this->getSerializer()->serialize($taxCategory, 'xml'));

        $this->assertEquals('TaxCategory', $xml->getName());
        $this->assertEquals(
            strval($taxCategory->id),
            strval($xml->xpath('*[local-name()="ID"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ID"]')[0]->getNamespaces()
        );
        $this->assertEquals(
            $taxCategory->percent,
            intval($xml->xpath('*[local-name()="Percent"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ID"]')[0]->getNamespaces()
        );

        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="TaxScheme"]')[0]->getNamespaces()
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
