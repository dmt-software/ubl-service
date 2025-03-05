<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice\Type\Amount;

class AmountTest extends AmountTestCase
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
}
