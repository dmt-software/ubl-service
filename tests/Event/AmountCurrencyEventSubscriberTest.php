<?php

namespace DMT\Test\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice\Price;
use DMT\Ubl\Service\Entity\Invoice\Type\Amount;
use DMT\Ubl\Service\Entity\Invoice\Type\AmountType;
use DMT\Ubl\Service\Event\AmountCurrencyEventSubscriber;
use JMS\Serializer\Builder\DefaultDriverFactory;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;
use Metadata\MetadataFactory;
use PHPUnit\Framework\TestCase;

class AmountCurrencyEventSubscriberTest extends TestCase
{
    public function testEventCreatesAmountEntry(): void
    {
        $subscriber = new AmountCurrencyEventSubscriber();

        $price = new Price();
        $price->priceAmount = 34.95;

        $subscriber->prepareXmlValue(
            $this->getPreSerializeEvent($price),
        );

        $this->assertInstanceOf(AmountType::class, $price->priceAmount);
        $this->assertEquals(34.95, $price->priceAmount->amount);
    }

    public function testEventAddsDefaultCurrency(): void
    {
        $subscriber = new AmountCurrencyEventSubscriber('EUR');

        $amount = new Amount();
        $amount->amount = 44.98;

        $this->assertNull($amount->currencyId);

        $subscriber->addDefaultCurrency(
            $this->getPreSerializeEvent($amount)
        );

        $this->assertEquals('EUR', $amount->currencyId);
    }

    public function testEventNoOverrideCurrency(): void
    {
        $subscriber = new AmountCurrencyEventSubscriber('EUR');

        $amount = new Amount();
        $amount->amount = 44.98;
        $amount->currencyId = 'USD';

        $subscriber->addDefaultCurrency(
            $this->getPreSerializeEvent($amount)
        );

        $this->assertEquals('USD', $amount->currencyId);
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
