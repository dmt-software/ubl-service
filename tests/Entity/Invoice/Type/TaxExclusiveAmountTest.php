<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice\Type\TaxExclusiveAmount;

class TaxExclusiveAmountTest extends AmountTestCase
{
    public static function provideAmount(): iterable
    {
        $amount = new TaxExclusiveAmount();
        $amount->amount = 342.24;
        $amount->currencyId = 'USD';

        yield 'default usage' => [$amount];

        $amount = new TaxExclusiveAmount();
        $amount->amount = 6832.54;

        yield 'set default currency' => [$amount];

        $amount = new TaxExclusiveAmount();
        $amount->amount = 9938.2267;
        $amount->currencyId = 'EUR';

        yield 'amount round up to max 2 decimals' => [$amount];
    }
}
