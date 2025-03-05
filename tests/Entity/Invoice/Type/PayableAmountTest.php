<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice\Type\PayableAmount;

class PayableAmountTest extends AmountTestCase
{
    public static function provideAmount(): iterable
    {
        $amount = new PayableAmount();
        $amount->amount = 1554.22;
        $amount->currencyId = 'USD';

        yield 'default usage' => [$amount];

        $amount = new PayableAmount();
        $amount->amount = 154.45;

        yield 'set default currency' => [$amount];

        $amount = new PayableAmount();
        $amount->amount = 154.2187;
        $amount->currencyId = 'EUR';

        yield 'amount round up to max 2 decimals' => [$amount];
    }
}
