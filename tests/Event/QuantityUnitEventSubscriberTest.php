<?php

namespace DMT\Test\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice\Type\BaseQuantity;
use DMT\Ubl\Service\Entity\Invoice\Type\QuantityType;
use DMT\Ubl\Service\Event\QuantityUnitEventSubscriber;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\SerializationContext;
use PHPUnit\Framework\TestCase;

class QuantityUnitEventSubscriberTest extends TestCase
{
    public function testEventAddsDefaultUnit(): void
    {
        $subscriber = new QuantityUnitEventSubscriber();

        $baseQuantity = new BaseQuantity();
        $baseQuantity->quantity = 3;

        $this->assertNull($baseQuantity->unitCode);

        $subscriber->addDefaultUnit(
            $this->getPreSerializeEvent($baseQuantity)
        );

        $this->assertNotNull($baseQuantity->unitCode);
    }

    public function testEventNoChangeToUnit(): void
    {
        $subscriber = new QuantityUnitEventSubscriber();

        $baseQuantity = new BaseQuantity();
        $baseQuantity->quantity = 3;
        $baseQuantity->unitCode = 'ST';

        $subscriber->addDefaultUnit(
            $this->getPreSerializeEvent($baseQuantity)
        );

        $this->assertEquals('ST', $baseQuantity->unitCode);
    }

    protected function getPreSerializeEvent(QuantityType $object): PreSerializeEvent
    {
        $name = $object::class;

        return new PreSerializeEvent(
            SerializationContext::create(),
            $object,
            compact('name')
        );
    }
}
