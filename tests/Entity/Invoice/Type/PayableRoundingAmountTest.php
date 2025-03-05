<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice\Type\PayableRoundingAmount;

class PayableRoundingAmountTest extends AmountTestCase
{
    public static function provideAmount(): iterable
    {
        $amount = new PayableRoundingAmount();
        $amount->amount = 0.02;
        $amount->currencyId = 'USD';

        yield 'default usage' => [$amount];

        $amount = new PayableRoundingAmount();
        $amount->amount = 0.01;

        yield 'set default currency' => [$amount];

        $amount = new PayableRoundingAmount();
        $amount->amount = 0.007;
        $amount->currencyId = 'EUR';

        yield 'amount round up to max 2 decimals' => [$amount];
    }
}
