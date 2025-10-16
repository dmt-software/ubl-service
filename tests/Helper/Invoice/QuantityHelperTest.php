<?php

namespace DMT\Test\Ubl\Service\Helper\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\BaseQuantity;
use DMT\Ubl\Service\Entity\Invoice\Type\InvoicedQuantity;
use DMT\Ubl\Service\Entity\Invoice\Type\QuantityType;
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
        $baseQuantity = new BaseQuantity();
        $baseQuantity->quantity = 1;

        yield 'from int' => [BaseQuantity::class, 1, $baseQuantity];
        yield 'from object' => [BaseQuantity::class, (object)['quantity' => 1], $baseQuantity];
        yield 'set from self' => [BaseQuantity::class, $baseQuantity, $baseQuantity];
        yield 'not set when null' => [BaseQuantity::class, null, null];

        $invoicedQuantity = new InvoicedQuantity();
        $invoicedQuantity->quantity = 15;
        $invoicedQuantity->unitCode = 'UA';

        yield 'set with unit' => [
            InvoicedQuantity::class,
            (object)['quantity' => 15, 'unitCode' => 'UA'],
            $invoicedQuantity
        ];
    }
}
