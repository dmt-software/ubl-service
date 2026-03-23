<?php

namespace DMT\Test\Ubl\Service\Entity\Components\Type;

use DMT\Ubl\Service\Entity\CommonBasicComponents\Amount;
use DMT\Ubl\Service\Entity\CommonBasicComponents\AmountType;
use DMT\Ubl\Service\Event\AmountCurrencyEventSubscriber;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

class AmountTest extends TestCase
{
    public static function provideAmount(): iterable
    {
        $amount = new Amount();
        $amount->amount = 12.34;
        $amount->currencyId = 'USD';

        yield 'default usage' => [$amount];

        $amount = new Amount();
        $amount->amount = 12.34;

        yield 'set default currency' => [$amount];

        $amount = new Amount();
        $amount->amount = 12.34567;
        $amount->currencyId = 'EUR';

        yield 'amount round up to max 2 decimals' => [$amount];
    }

    #[DataProvider(methodName: "provideAmount")]
    public function testSerialize(AmountType $amount): void
    {
        $xml = simplexml_load_string($this->getSerializer()->serialize($amount, 'xml'));

        $this->assertEquals((new ReflectionObject($amount))->getShortName(), $xml->getName());
        $this->assertEquals(round($amount->amount, 2), strval($xml));
        $this->assertEquals($amount->currencyId, $xml['currencyID']);
        $this->assertStringStartsWith(round($amount->amount, 2), strval($amount));
        $this->assertStringEndsWith($amount->currencyId, strval($amount));
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->getDocNamespaces()
        );
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
