<?php

namespace DMT\Test\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice\Price;
use DMT\Ubl\Service\Entity\Invoice\Type\BaseQuantity;
use DMT\Ubl\Service\Entity\Invoice\Type\QuantityType;
use DMT\Ubl\Service\Event\QuantityUnitEventSubscriber;
use JMS\Serializer\Builder\DefaultDriverFactory;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;
use Metadata\MetadataFactory;
use PHPUnit\Framework\TestCase;

class QuantityUnitEventSubscriberTest extends TestCase
{
    public function testEventCreatesQuantityEntry(): void
    {
        $subscriber = new QuantityUnitEventSubscriber();

        $price = new Price();
        $price->baseQuantity = 2;

        $subscriber->prepareXmlValue(
            $this->getPreSerializeEvent($price),
        );

        $this->assertInstanceOf(QuantityType::class, $price->baseQuantity);
        $this->assertEquals(2, $price->baseQuantity->quantity);
    }

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

    protected function getPreSerializeEvent(object $object): PreSerializeEvent
    {
        $factory = new DefaultDriverFactory(
            new SerializedNameAnnotationStrategy(
                new IdenticalPropertyNamingStrategy()
            )
        );

        $context = $this->getMockBuilder(SerializationContext::class)
            ->onlyMethods(['getMetadataFactory'])
            ->disableOriginalConstructor()
            ->getMock();

        $context->expects($this->any())->method('getMetadataFactory')->willReturnCallback(
            fn() => new MetadataFactory($factory->createDriver([]))
        );

        $name = $object::class;

        return new PreSerializeEvent(
            $context,
            $object,
            compact('name')
        );
    }
}
