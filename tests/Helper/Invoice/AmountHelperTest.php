<?php

namespace DMT\Test\Ubl\Service\Helper\Invoice;

use DMT\Ubl\Service\Entity\CommonBasicComponents\Amount;
use DMT\Ubl\Service\Entity\CommonBasicComponents\AmountType;
use DMT\Ubl\Service\Helper\Invoice\AmountHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AmountHelperTest extends TestCase
{
    #[DataProvider(methodName: 'provideAmount')]
    public function testFetchFromValue(string $amountType, mixed $value, null|AmountType $expected): void
    {
        $this->assertEquals($expected, AmountHelper::fetchFromValue($value, $amountType));
    }

    public static function provideAmount(): iterable
    {
        $priceAmount = new Amount();
        $priceAmount->amount = 4.335;

        yield 'from scalar value' => [Amount::class, 4.335, $priceAmount];

        $taxAmount = new Amount();
        $taxAmount->amount = 100.00;
        $taxAmount->currencyId = 'EUR';

        yield 'from object' => [Amount::class, (object)['amount' => 100.00, 'currencyId' => 'EUR'], $taxAmount];
        yield 'not set from array' => [Amount::class, ['amount' => 123], null];
        yield 'set from self' => [Amount::class, $priceAmount, $priceAmount];
        yield 'not set when null' => [Amount::class, null, null];
    }
}
