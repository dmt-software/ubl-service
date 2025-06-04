<?php

namespace DMT\Test\Ubl\Service\Format;

use DMT\Ubl\Service\Format\VatNumberBE;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VatNumberBETest extends TestCase
{
    #[DataProvider('validVatNumberProvider')]
    public function testFormatValidVatNumber(string $identifier, string $expected): void
    {
        $this->assertSame($expected, (new VatNumberBE())->format($identifier));
    }

    public static function validVatNumberProvider(): iterable
    {
        return [
            ['1234567894', 'BE1234567894'],
            ['BE0123456749', 'BE0123456749'],
        ];
    }

    #[DataProvider('invalidVatNumberProvider')]
    public function testFormatInvalidVatNumber(string $identifier): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new VatNumberBE())->format($identifier);
    }

    public static function invalidVatNumberProvider(): array
    {
        return [
            'wrong country code' => ['NL003366722B01'],
            'does not start with 0 or 1' => ['2233123123'],
            'mod 97 check fails' => ['0123123142'],
        ];
    }
}