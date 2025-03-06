<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice\Type\QuantityType;
use DMT\Ubl\Service\Event\QuantityUnitEventSubscriber;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

abstract class QuantityTestCase extends TestCase
{
    abstract public static function provideQuantity(): iterable;

    #[DataProvider(methodName: "provideQuantity")]
    public function testAmount(QuantityType $quantity): void
    {
        $xml = simplexml_load_string($this->getSerializer()->serialize($quantity, 'xml'));

        $this->assertEquals((new ReflectionObject($quantity))->getShortName(), $xml->getName());
        $this->assertEquals($quantity->quantity, strval($xml));
        $this->assertEquals($quantity->unitCode, $xml['unitCode']);
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->getDocNamespaces()
        );
    }

    protected function getSerializer(): Serializer
    {
        $builder = SerializerBuilder::create();
        $builder->configureListeners(function (EventDispatcher $dispatcher) {
            $dispatcher->addSubscriber(new QuantityUnitEventSubscriber('EUR'));
        });

        return $builder->build();
    }
}
