<?php

namespace DMT\Test\Ubl\Service\Format;

use DMT\Ubl\Service\Format\VatNumberDE;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VatNumberDETest extends TestCase
{
    #[DataProvider('validVatNumberProvider')]
    public function testValidVatNumberFormats(string $identifier, string $expected): void
    {
        $this->assertSame($expected, (new VatNumberDE())->format($identifier));
    }

    public static function validVatNumberProvider(): iterable
    {
        return [
            ['120347124', 'DE120347124'],
            ['DE811256202', 'DE811256202'],
            ['de111133721', 'DE111133721'],
            ['813182233', 'DE813182233'],
        ];
    }

    #[DataProvider('invalidVatNumberProvider')]
    public function testInvalidVatNumberFormats(string $identifier): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new VatNumberDE())->format($identifier);
    }

    public static function invalidVatNumberProvider(): array
    {
        return [
            'wrong country code' => ['NL003366722B01'],
            'number to long' => ['2233123123'],
            'mod 11, 10 fails' => ['123123142'],
        ];
    }
}
