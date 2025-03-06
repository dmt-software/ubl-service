<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Address;
use DMT\Ubl\Service\Event\NormalizeAddressEventSubscriber;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    public function testVersion20000(): void
    {
        $context = SerializationContext::create()->setVersion('2.0');

        $address = $this->getAddress();

        $xml = simplexml_load_string($this->getSerializer()->serialize($address, 'xml', $context));

        $this->assertEquals('Address', $xml->getName());
        $this->assertStringStartsWith($address->streetName, strval($xml->xpath('*[local-name()="StreetName"]')[0]));
        $this->assertStringEndsWith($address->buildingNumber, strval($xml->xpath('*[local-name()="StreetName"]')[0]));
        $this->assertEquals(
            $address->additionalStreetName,
            strval($xml->xpath('*[local-name()="AdditionalStreetName"]')[0])
        );
        $this->assertEquals($address->cityName, $xml->xpath('*[local-name()="CityName"]')[0]);
    }

    public function getVersion10000(): void
    {
        $context = SerializationContext::create()->setVersion('2.0');

        $address = $this->getAddress();

        $xml = simplexml_load_string($this->getSerializer()->serialize($address, 'xml', $context));

        $this->assertEquals('Address', $xml->getName());
        $this->assertEquals($address->streetName, strval($xml->xpath('*[local-name()="StreetName"]')[0]));
        $this->assertEquals($address->buildingNumber, strval($xml->xpath('*[local-name()="BuildingNumber"]')[0]));
        $this->assertEmpty($xml->xpath('*[local-name()="AdditionalStreetName"]'));
        $this->assertEquals($address->cityName, $xml->xpath('*[local-name()="CityName"]')[0]);
    }

    protected function getAddress(): Address
    {
        $address = new Address();
        $address->streetName = 'Somestreet';
        $address->buildingNumber = '2';
        $address->cityName = 'City';
        $address->additionalStreetName = '2nd door on the right';

        return $address;
    }

    public function getSerializer(): Serializer
    {
        $builder = SerializerBuilder::create();
        $builder->configureListeners(function (EventDispatcher $dispatcher) {
            $dispatcher->addSubscriber(new NormalizeAddressEventSubscriber());
        });

        return $builder->build();
    }
}
