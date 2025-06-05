<?php

namespace DMT\Test\Ubl\Service\Format;

use DMT\Ubl\Service\Format\GlobalTradeItemNumber;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GlobalTradeItemNumberTest extends TestCase
{
    #[DataProvider('validGTINProvider')]
    public function testValidGTINFormats(string $identifier, ?int $size, string $expected): void
    {
        $this->assertEquals($expected, (new GlobalTradeItemNumber($size))->format($identifier));
    }

    public static function validGTINProvider(): array
    {
        return [
            'GTIN-8' => ['12345670', null, '12345670'],
            'GTIN-12' => ['012345678905', null, '012345678905'],
            'GTIN-13' => ['4006381333931', null, '4006381333931'],
            'GTIN-14' => ['12345678901231', null, '12345678901231'],
            'GTIN-8 with fixed size' => ['12345670', 8, '12345670'],
            'GTIN-13 with fixed size' => ['4006381333931', 13, '4006381333931'],
        ];
    }

    #[DataProvider('invalidGTINProvider')]
    public function testInvalidGTINFormats(string $identifier, ?int $size): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new GlobalTradeItemNumber($size))->format($identifier);
    }

    public static function invalidGTINProvider(): array
    {
        return [
            'invalid size' => ['123456789', null],
            'invalid characters' => ['ABC123456789', null],
            'invalid checksum' => ['4006381333932', null],
            'mismatch with specific size' => ['12345670', 13],
        ];
    }
}