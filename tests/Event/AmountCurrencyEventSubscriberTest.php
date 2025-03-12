<?php

namespace DMT\Test\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice\Type\Amount;
use DMT\Ubl\Service\Entity\Invoice\Type\AmountType;
use DMT\Ubl\Service\Event\AmountCurrencyEventSubscriber;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\SerializationContext;
use PHPUnit\Framework\TestCase;

class AmountCurrencyEventSubscriberTest extends TestCase
{
    public function testEventAddsDefaultCurrency(): void
    {
        $subscriber = new AmountCurrencyEventSubscriber('EUR');

        $amount = new Amount();
        $amount->amount = 44.98;

        $this->assertNull($amount->currencyId);

        $subscriber->addDefaultCurrency(
            $this->getPreSerializeEvent(
                $amount, [
                    'name' => Amount::class
                ]
            )
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

    protected function getPreSerializeEvent(AmountType $object): PreSerializeEvent
    {
        $name = $object::class;

        return new PreSerializeEvent(
            SerializationContext::create(),
            $object,
            compact('name')
        );
    }
}
