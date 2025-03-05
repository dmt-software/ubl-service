<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice\Type\TaxAmount;

class TaxAmountTest extends AmountTestCase
{
    public static function provideAmount(): iterable
    {
        $amount = new TaxAmount();
        $amount->amount = 144.12;
        $amount->currencyId = 'USD';

        yield 'default usage' => [$amount];

        $amount = new TaxAmount();
        $amount->amount = 99.99;

        yield 'set default currency' => [$amount];

        $amount = new TaxAmount();
        $amount->amount = 3.1415;
        $amount->currencyId = 'EUR';

        yield 'amount round up to max 2 decimals' => [$amount];
    }
}
