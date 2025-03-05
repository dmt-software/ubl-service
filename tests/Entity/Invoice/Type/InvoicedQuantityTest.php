<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice\Type\InvoicedQuantity;

class InvoicedQuantityTest extends QuantityTestCase
{
    public static function provideQuantity(): iterable
    {
        $quantity = new InvoicedQuantity();
        $quantity->quantity = 1;
        $quantity->unitCode = 'ZZ';

        yield 'default usage' => [$quantity];

        $quantity = new InvoicedQuantity();
        $quantity->quantity = 10;

        yield 'sets default unit code' => [$quantity];
    }
}
