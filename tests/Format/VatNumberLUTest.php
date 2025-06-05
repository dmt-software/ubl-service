<?php

namespace DMT\Test\Ubl\Service\Format;

use DMT\Ubl\Service\Format\VatNumberLU;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VatNumberLUTest extends TestCase
{
    #[DataProvider('provideValidVatNumbers')]
    public function testValidVatNumberFormats(string $identifier, string $expected): void
    {
        $this->assertSame($expected, (new VatNumberLU())->format($identifier));
    }

    public static function provideValidVatNumbers(): iterable
    {
        return [
            'valid VAT with LU prefix' => ['LU12345613', 'LU12345613'],
            'valid VAT without prefix' => ['23456752', 'LU23456752'],
            'valid VAT with spaces' => ['LU98765421  ', 'LU98765421'],
        ];
    }

    #[DataProvider('provideInvalidVatNumbers')]
    public function testInvalidVatNumberFormats(string $identifier): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new VatNumberLU())->format($identifier);
    }

    public static function provideInvalidVatNumbers(): iterable
    {
        return [
            'invalid checksum' => ['LU12345679'],
            'too short' => ['LU1234567'],
            'too long' => ['LU123456789'],
            'contains non-numeric characters' => ['LU12345A8'],
        ];
    }
}