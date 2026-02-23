<?php

namespace DMT\Test\Ubl\Service\Format;

use DMT\Ubl\Service\Format\VatNumberDK;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VatNumberDKTest extends TestCase
{
    #[DataProvider('validVatNumberProvider')]
    public function testValidVatNumberFormats(string $identifier, string $expected): void
    {
        $this->assertSame($expected, (new VatNumberDK())->format($identifier));
    }

    public static function validVatNumberProvider(): iterable
    {
        return [
            ['47458714', 'DK47458714'],
            ['DK22756214', 'DK22756214'],
            ['54562519', 'DK54562519'],
            ['dk41023821', 'DK41023821'],
        ];
    }

    #[DataProvider('invalidVatNumberProvider')]
    public function testInvalidVatNumberFormats(string $identifier): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new VatNumberDK())->format($identifier);
    }

    public static function invalidVatNumberProvider(): array
    {
        return [
            'wrong country code' => ['NL003366722B01'],
            'number to long' => ['2233123123'],
            'weighted mod 11 fails' => ['23123143'],
        ];
    }
}
