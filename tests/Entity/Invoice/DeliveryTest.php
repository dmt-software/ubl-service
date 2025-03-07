<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Address;
use DMT\Ubl\Service\Entity\Invoice\Delivery;
use DMT\Ubl\Service\Entity\Invoice\DeliveryLocation;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class DeliveryTest extends TestCase
{
    public function testSerialize(): void
    {
        $delivery = new Delivery();
        $delivery->deliveryLocation = new DeliveryLocation();
        $delivery->deliveryLocation->address = new Address();
        $delivery->deliveryLocation->address->cityName = 'City';

        $xml = simplexml_load_string($this->getSerializer()->serialize($delivery, 'xml'));

        $this->assertEquals('Delivery', $xml->getName());
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="DeliveryLocation"]')[0]->getNamespaces()
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
