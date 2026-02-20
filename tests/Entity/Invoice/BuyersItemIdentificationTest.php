<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\BuyersItemIdentification;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class BuyersItemIdentificationTest extends TestCase
{
    public function testSerialize(): void
    {
        $buyersItemIdentification = new BuyersItemIdentification();
        $buyersItemIdentification->id = 'AD002874';

        $xml = simplexml_load_string($this->getSerializer()->serialize($buyersItemIdentification, 'xml'));

        $this->assertEquals('SellersItemIdentification', $xml->getName());
        $this->assertEquals(
            $buyersItemIdentification->id,
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
