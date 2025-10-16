<?php

namespace DMT\Test\Ubl\Service\Helper\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\AmountType;
use DMT\Ubl\Service\Entity\Invoice\Type\PriceAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\TaxAmount;
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
        $priceAmount = new PriceAmount();
        $priceAmount->amount = 4.335;

        yield 'from scalar value' => [PriceAmount::class, 4.335, $priceAmount];

        $taxAmount = new TaxAmount();
        $taxAmount->amount = 100.00;
        $taxAmount->currencyId = 'EUR';

        yield 'from object' => [TaxAmount::class, (object)['amount' => 100.00, 'currencyId' => 'EUR'], $taxAmount];
        yield 'not set from array' => [PriceAmount::class, ['amount' => 123], null];
        yield 'set from self' => [PriceAmount::class, $priceAmount, $priceAmount];
        yield 'not set when null' => [TaxAmount::class, null, null];
    }
}
