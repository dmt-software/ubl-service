<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice\Type\AmountType;
use DMT\Ubl\Service\Event\AmountCurrencyEventSubscriber;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

abstract class AmountTestCase extends TestCase
{
    abstract public static function provideAmount(): iterable;

    #[DataProvider(methodName: "provideAmount")]
    public function testAmount(AmountType $amount): void
    {
        $xml = simplexml_load_string($this->getSerializer()->serialize($amount, 'xml'));

        $this->assertEquals((new ReflectionObject($amount))->getShortName(), $xml->getName());
        $this->assertEquals(round($amount->amount, 2), strval($xml));
        $this->assertEquals($amount->currencyId, $xml['currencyId']);
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