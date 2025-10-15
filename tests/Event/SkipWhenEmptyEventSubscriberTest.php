<?php

namespace DMT\Test\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice\LegalMonetaryTotal;
use DMT\Ubl\Service\Entity\Invoice\Type\AllowanceTotalAmount;
use DMT\Ubl\Service\Event\SkipWhenEmptyEventSubscriber;
use JMS\Serializer\Builder\DefaultDriverFactory;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;
use Metadata\MetadataFactory;
use PHPUnit\Framework\TestCase;

class SkipWhenEmptyEventSubscriberTest extends TestCase
{
    public function testEmptyElements(): void
    {
        $allowanceTotalAmount = new AllowanceTotalAmount();
        $allowanceTotalAmount->setCurrency('EUR');

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

        $event = new PreSerializeEvent(
            $context,
            $allowanceTotalAmount,
            ['name' => AllowanceTotalAmount::class]
        );

        $subscriber = new SkipWhenEmptyEventSubscriber();
        $subscriber->emptyElements($event);

        $this->assertNull($allowanceTotalAmount->amount);
        $this->assertNull($allowanceTotalAmount->currencyId);


        $legalMonetaryTotal = new LegalMonetaryTotal();
        $legalMonetaryTotal->allowanceTotalAmount = new AllowanceTotalAmount();

        $event = new PreSerializeEvent(
            clone($context),
            $legalMonetaryTotal,
            ['name' => LegalMonetaryTotal::class]
        );

        $subscriber = new SkipWhenEmptyEventSubscriber();
        $subscriber->emptyElements($event);
    }
}

