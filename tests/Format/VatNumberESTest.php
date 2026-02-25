<?php

namespace DMT\Test\Ubl\Service\Format;

use DMT\Ubl\Service\Format\VatNumberES;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VatNumberESTest extends TestCase
{
    #[DataProvider('validVatNumberProvider')]
    public function testValidVatNumberFormats(string $identifier, string $expected): void
    {
        $this->assertSame($expected, (new VatNumberES())->format($identifier));
    }

    public static function validVatNumberProvider(): iterable
    {
        return [
            ['ESA28000032', 'ESA28000032'],
            ['W0123456G', 'ESW0123456G'],
            ['X1234567L', 'ESX1234567L'],
            ['ES12345678Z', 'ES12345678Z'],
        ];
    }

    #[DataProvider('provideInvalidVatNumbers')]
    public function testInvalidVatNumberFormats(string $identifier): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new VatNumberES())->format($identifier);
    }

    public static function provideInvalidVatNumbers(): iterable
    {
        return [
            'invalid checksum' => ['ESB28000033'],
            'too short' => ['ES1234567X'],
            'unknown company type' => ['ESI28000032'],
            'wrong country' => ['LU12345678Z'],
        ];
    }
}
