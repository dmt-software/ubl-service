<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice\Type\LineExtensionAmount;

class LineExtensionAmountTest extends AmountTestCase
{
    public static function provideAmount(): iterable
    {
        $amount = new LineExtensionAmount();
        $amount->amount = 4389.24;
        $amount->currencyId = 'EUR';

        yield 'default usage' => [$amount];

        $amount = new LineExtensionAmount();
        $amount->amount = 49.95;

        yield 'set default currency' => [$amount];

        $amount = new LineExtensionAmount();
        $amount->amount = 0.057;
        $amount->currencyId = 'EUR';

        yield 'amount round up to max 2 decimals' => [$amount];
    }
}
