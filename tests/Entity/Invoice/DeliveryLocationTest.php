<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Address;
use DMT\Ubl\Service\Entity\Invoice\DeliveryLocation;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class DeliveryLocationTest extends TestCase
{
    public function testSerialize(): void
    {
        $deliveryLocation = new DeliveryLocation();
        $deliveryLocation->address = new Address();
        $deliveryLocation->address->streetName = 'Nowhere 1';

        $xml = simplexml_load_string($this->getSerializer()->serialize($deliveryLocation, 'xml'));

        $this->assertEquals('DeliveryLocation', $xml->getName());
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="Address"]')[0]->getNamespaces()
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
