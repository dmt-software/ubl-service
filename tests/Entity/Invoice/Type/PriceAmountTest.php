<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice\Type\PriceAmount;
use DMT\Ubl\Service\Event\AmountCurrencyEventSubscriber;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

class PriceAmountTest extends TestCase
{
    #[DataProvider(methodName: "provideAmount")]
    public function testAmount(PriceAmount $amount): void
    {
        $xml = simplexml_load_string($this->getSerializer()->serialize($amount, 'xml'));

        $this->assertEquals((new ReflectionObject($amount))->getShortName(), $xml->getName());
        $this->assertEquals($amount->amount, strval($xml));
        $this->assertEquals($amount->currencyId, $xml['currencyId']);
        $this->assertStringStartsWith($amount->amount, strval($amount));
        $this->assertStringEndsWith($amount->currencyId, strval($amount));
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->getDocNamespaces()
        );
    }

    public static function provideAmount(): iterable
    {
        $amount = new PriceAmount();
        $amount->amount = 1554.228;
        $amount->currencyId = 'USD';

        yield 'default usage (not rounded)' => [$amount];

        $amount = new PriceAmount();
        $amount->amount = 154.45;

        yield 'set default currency' => [$amount];
    }

    protected function getSerializer(): Serializer
    {
        $builder = SerializerBuilder::create();
        $builder->configureListeners(function (EventDispatcher $dispatcher) {
            $dispatcher->addSubscriber(new AmountCurrencyEventSubscriber('EUR'));
        });
        return $builder->build();
    }
}
