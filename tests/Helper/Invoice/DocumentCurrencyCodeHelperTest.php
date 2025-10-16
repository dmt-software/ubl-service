<?php

namespace DMT\Test\Ubl\Service\Helper\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\DocumentCurrencyCode;
use DMT\Ubl\Service\Helper\Invoice\DocumentCurrencyCodeHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DocumentCurrencyCodeHelperTest extends TestCase
{
    #[DataProvider(methodName: 'provideDocumentCurrencyCode')]
    public function testFetchFromValue(mixed $value, null|DocumentCurrencyCode $expected): void
    {
        $this->assertEquals($expected, DocumentCurrencyCodeHelper::fetchFromValue($value));
    }

    public static function provideDocumentCurrencyCode(): iterable
    {
        $documentCurrencyCode = new DocumentCurrencyCode();
        $documentCurrencyCode->code = 'EUR';

        yield 'from string' => ['EUR', $documentCurrencyCode];
        yield 'from object' => [(object)['code' => 'EUR'], $documentCurrencyCode];
        yield 'set from self' => [$documentCurrencyCode, $documentCurrencyCode];
        yield 'not set when empty' => ['', null];
    }
}
