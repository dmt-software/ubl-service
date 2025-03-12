<?php

namespace DMT\Test\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\Address;
use DMT\Ubl\Service\Event\NormalizeAddressEventSubscriber;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\SerializationContext;
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
