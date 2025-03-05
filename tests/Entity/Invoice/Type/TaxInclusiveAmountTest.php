<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice\Type\TaxInclusiveAmount;

class TaxInclusiveAmountTest extends AmountTestCase
{
    public static function provideAmount(): iterable
    {
        $amount = new TaxInclusiveAmount();
        $amount->amount = 44.15;
        $amount->currencyId = 'SEK';

        yield 'default usage' => [$amount];

        $amount = new TaxInclusiveAmount();
        $amount->amount = 6.95;

        yield 'set default currency' => [$amount];

        $amount = new TaxInclusiveAmount();
        $amount->amount = 109.552;
        $amount->currencyId = 'EUR';

        yield 'amount round up to max 2 decimals' => [$amount];
    }
}
