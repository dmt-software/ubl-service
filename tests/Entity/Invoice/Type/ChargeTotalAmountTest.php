<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice\Type\ChargeTotalAmount;

class ChargeTotalAmountTest extends AmountTestCase
{
    public static function provideAmount(): iterable
    {
        $amount = new ChargeTotalAmount();
        $amount->amount = 323.24;
        $amount->currencyId = 'DKK';

        yield 'default usage' => [$amount];

        $amount = new ChargeTotalAmount();
        $amount->amount = 4995.32;

        yield 'set default currency' => [$amount];

        $amount = new ChargeTotalAmount();
        $amount->amount = 48.242;
        $amount->currencyId = 'EUR';

        yield 'amount round up to max 2 decimals' => [$amount];
    }
}
