<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Country;
use DMT\Ubl\Service\Entity\Invoice\PostalAddress;
use DMT\Ubl\Service\Entity\Invoice\Type\IdentificationCode;
use DMT\Ubl\Service\Event\NormalizeAddressEventSubscriber;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class PostalAddressTest extends TestCase
{
    public function testSerializeVersion20000(): void
    {
        $context = SerializationContext::create()->setVersion('2.0');

        $address = $this->getAddress();

        $xml = simplexml_load_string($this->getSerializer()->serialize($address, 'xml', $context));

        $this->assertEquals('PostalAddress', $xml->getName());
        $this->assertStringStartsWith(
            $address->streetName,
            strval($xml->xpath('*[local-name()="StreetName"]')[0])
        );
        $this->assertStringEndsWith(
            $address->buildingNumber,
            strval($xml->xpath('*[local-name()="StreetName"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="StreetName"]')[0]->getNamespaces()
        );
        $this->assertEquals(
            $address->additionalStreetName,
            strval($xml->xpath('*[local-name()="AdditionalStreetName"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="AdditionalStreetName"]')[0]->getNamespaces()
        );
        $this->assertEquals(
            $address->cityName,
            strval($xml->xpath('*[local-name()="CityName"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="CityName"]')[0]->getNamespaces()
        );
        $this->assertEquals(
            $address->postalZone,
            strval($xml->xpath('*[local-name()="PostalZone"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="PostalZone"]')[0]->getNamespaces()
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="Country"]')[0]->getNamespaces()
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->getDocNamespaces()
        );
    }

    public function testSerializeVersion10000(): void
    {
        $context = SerializationContext::create()->setVersion('1.0');

        $address = $this->getAddress();

        $xml = simplexml_load_string($this->getSerializer()->serialize($address, 'xml', $context));

        $this->assertEquals('PostalAddress', $xml->getName());
        $this->assertEquals(
            $address->streetName,
            strval($xml->xpath('*[local-name()="StreetName"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="StreetName"]')[0]->getNamespaces()
        );
        $this->assertEquals(
            $address->buildingNumber,
            strval($xml->xpath('*[local-name()="BuildingNumber"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="BuildingNumber"]')[0]->getNamespaces()
        );
        $this->assertEmpty($xml->xpath('*[local-name()="AdditionalStreetName"]'));

        $this->assertEquals(
            $address->cityName,
            strval($xml->xpath('*[local-name()="CityName"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="CityName"]')[0]->getNamespaces()
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="Country"]')[0]->getNamespaces()
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->getDocNamespaces()
        );
    }

    protected function getAddress(): PostalAddress
    {
        $address = new PostalAddress();
        $address->streetName = 'Somestreet';
        $address->buildingNumber = '2';
        $address->cityName = 'City';
        $address->additionalStreetName = '2nd door on the right';
        $address->postalZone = '1234AB';
        $address->country = new Country();
        $address->country->identificationCode = new IdentificationCode();
        $address->country->identificationCode->code = 'US';

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
