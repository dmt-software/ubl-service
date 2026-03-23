<?php

namespace DMT\Test\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\CommonAggregateComponents\LegalMonetaryTotal;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Amount;
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
        $amount = new Amount();
        $amount->setCurrency('EUR');

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
            $amount,
            ['name' => Amount::class]
        );

        $subscriber = new SkipWhenEmptyEventSubscriber();
        $subscriber->emptyElements($event);

        $this->assertNull($amount->amount);
        $this->assertNull($amount->currencyId);

        $legalMonetaryTotal = new LegalMonetaryTotal();
        $legalMonetaryTotal->allowanceTotalAmount = new Amount();

        $event = new PreSerializeEvent(
            clone($context),
            $legalMonetaryTotal,
            ['name' => LegalMonetaryTotal::class]
        );

        $subscriber = new SkipWhenEmptyEventSubscriber();
        $subscriber->emptyElements($event);

        $this->assertNull($legalMonetaryTotal->allowanceTotalAmount);
    }
}

