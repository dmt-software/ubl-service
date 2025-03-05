<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice\Type\BaseQuantity;

class BaseQuantityTest extends QuantityTestCase
{
    public static function provideQuantity(): iterable
    {
        $quantity = new BaseQuantity();
        $quantity->quantity = 1;
        $quantity->unitCode = 'ZZ';

        yield 'default usage' => [$quantity];

        $quantity = new BaseQuantity();
        $quantity->quantity = 10;

        yield 'sets default unit code' => [$quantity];
    }
}
