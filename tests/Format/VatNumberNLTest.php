<?php

namespace DMT\Test\Ubl\Service\Format;

use DMT\Ubl\Service\Format\VatNumberNL;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VatNumberNLTest extends TestCase
{
    #[DataProvider('provideValidVatNumbers')]
    public function testValidNumberFormats(string $identifier, string $expected): void
    {
        $this->assertSame($expected, (new VatNumberNL())->format($identifier));
    }

    public static function provideValidVatNumbers(): array
    {
        return [
            'business without prefix' => ['123456782B01', 'NL123456782B01'],
            'sole propriety with prefix' => ['NL000000000B13', 'NL000000000B13'],
            'business lowercase' => ['nl123456782b01', 'NL123456782B01'],
            'sole propriety untrimmed' => ['  000000000B13', 'NL000000000B13'],
        ];
    }

    #[DataProvider('provideInvalidVatNumbers')]
    public function testInvalidNumberFormats(string $identifier): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new VatNumberNL())->format($identifier);
    }

    public static function provideInvalidVatNumbers(): array
    {
        return [
            'sole propriety fails' => ['NL123456789B02'],
            'business fails' => ['987654321B01'],
            'incorrect format' => ['NL123B01'],
        ];
    }
}