<?php

namespace DMT\Test\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\Address;
use DMT\Ubl\Service\Event\NormalizeAddressEventSubscriber;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\SerializationContext;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NormalizeAddressEventSubscriberTest extends TestCase
{
    public function testEventNormalizesAddress(): void
    {
        $subscriber = new NormalizeAddressEventSubscriber();

        $address = new Address();
        $address->streetName = 'Street of nowhere';
        $address->buildingNumber = '12';

        $subscriber->normalizeAddress(
            $this->getPreSerializeEvent($address, Invoice::VERSION_NLCIUS)
        );

        $this->assertEquals('Street of nowhere 12', $address->streetName);
    }

    public function testEventNoChangeInAddress(): void
    {
        $subscriber = new NormalizeAddressEventSubscriber();

        $address = new Address();
        $address->streetName = 'Street of nowhere';
        $address->buildingNumber = '12';

        $subscriber->normalizeAddress(
            $this->getPreSerializeEvent($address, Invoice::VERSION_1_1)
        );

        $this->assertEquals('Street of nowhere', $address->streetName);
        $this->assertEquals('12', $address->buildingNumber);
    }

    #[DataProvider(methodName: 'provideFullAddress')]
    public function testSplitAddress(string $addressLine, string $street, string $number): void
    {
        $subscriber = new NormalizeAddressEventSubscriber();
        $address = new Address();
        $address->streetName = $addressLine;

        $subscriber->normalizeAddress(
            $this->getPreSerializeEvent($address, Invoice::VERSION_1_1)
        );

        $this->assertEquals($street, $address->streetName);
        $this->assertEquals($number, $address->buildingNumber);
    }

    public static function provideFullAddress(): iterable
    {
        return [
            'street with number' => ['Street of nowhere 12', 'Street of nowhere', '12'],
            'street with number and addition' => ['Street of nowhere 12 B', 'Street of nowhere', '12 B'],
            'street containing number' => ['plein 40-45 12', 'plein 40-45', '12'],
            'street containing number with addition' => ['plein 40-45 23c', 'plein 40-45', '23c'],
        ];
    }

    protected function getPreSerializeEvent(object $object, string $version = '2.0'): PreSerializeEvent
    {
        $name = $object::class;

        return new PreSerializeEvent(
            SerializationContext::create()->setVersion($version),
            $object,
            compact('name')
        );
    }
}
