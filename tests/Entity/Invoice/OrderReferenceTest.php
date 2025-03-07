<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\OrderReference;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class OrderReferenceTest extends TestCase
{
    public function testSerialize(): void
    {
        $orderReference = new OrderReference();
        $orderReference->id = '1443255';

        $xml = simplexml_load_string($this->getSerializer()->serialize($orderReference, 'xml'));

        $this->assertEquals('OrderReference', $xml->getName());
        $this->assertEquals(
            $orderReference->id,
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
