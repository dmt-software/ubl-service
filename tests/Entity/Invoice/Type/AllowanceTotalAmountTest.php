<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice\Type\AllowanceTotalAmount;

class AllowanceTotalAmountTest extends AmountTestCase
{
    public static function provideAmount(): iterable
    {
        $amount = new AllowanceTotalAmount();
        $amount->amount = 56.78;
        $amount->currencyId = 'USD';

        yield 'default usage' => [$amount];

        $amount = new AllowanceTotalAmount();
        $amount->amount = 56.78;

        yield 'set default currency' => [$amount];

        $amount = new AllowanceTotalAmount();
        $amount->amount = 56.7890;
        $amount->currencyId = 'EUR';

        yield 'amount round up to max 2 decimals' => [$amount];
    }
}
