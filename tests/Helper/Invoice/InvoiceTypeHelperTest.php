<?php

namespace DMT\Test\Ubl\Service\Helper\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\InvoiceTypeCode;
use DMT\Ubl\Service\Helper\Invoice\InvoiceTypeHelper;
use DMT\Ubl\Service\List\InvoiceType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class InvoiceTypeHelperTest extends TestCase
{
    #[DataProvider(methodName: 'provideInvoiceTypeCode')]
    public function testFetchFromValue(mixed $value, InvoiceTypeCode $expected): void
    {
        $this->assertEquals($expected, InvoiceTypeHelper::fetchFromValue($value));
    }

    public static function provideInvoiceTypeCode(): iterable
    {
        $invoiceTypeCode = new InvoiceTypeCode();
        $invoiceTypeCode->code = InvoiceType::Normal;

        $debetTypeCode = new InvoiceTypeCode();
        $debetTypeCode->code = InvoiceType::Debit;

        yield 'from string' => ['380', $invoiceTypeCode];
        yield 'from object' => [(object)['code' => '383'], $debetTypeCode];
        yield 'set from self' => [$invoiceTypeCode, $invoiceTypeCode];
        yield 'set default' => [null, $invoiceTypeCode];
    }
}
