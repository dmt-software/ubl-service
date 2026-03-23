<?php

namespace DMT\Test\Ubl\Service\Helper\Invoice;

use DMT\Ubl\Service\Entity\CommonBasicComponents\Quantity;
use DMT\Ubl\Service\Entity\CommonBasicComponents\QuantityType;
use DMT\Ubl\Service\Helper\Invoice\QuantityHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QuantityHelperTest extends TestCase
{
    #[DataProvider(methodName: 'provideQuantity')]
    public function testFetchFromValue(string $quantityType, mixed $value, null|QuantityType $expected): void
    {
        $this->assertEquals($expected, QuantityHelper::fetchFromValue($value, $quantityType));
    }

    public static function provideQuantity(): iterable
    {
        $baseQuantity = new Quantity();
        $baseQuantity->quantity = 1;

        yield 'from int' => [Quantity::class, 1, $baseQuantity];
        yield 'from object' => [Quantity::class, (object)['quantity' => 1], $baseQuantity];
        yield 'set from self' => [Quantity::class, $baseQuantity, $baseQuantity];
        yield 'not set when null' => [Quantity::class, null, null];

        $invoicedQuantity = new Quantity();
        $invoicedQuantity->quantity = 15;
        $invoicedQuantity->unitCode = 'UA';

        yield 'set with unit' => [
            Quantity::class,
            (object)['quantity' => 15, 'unitCode' => 'UA'],
            $invoicedQuantity
        ];
    }
}
