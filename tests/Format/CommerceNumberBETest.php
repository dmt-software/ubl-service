<?php

namespace DMT\Test\Ubl\Service\Format;

use DMT\Ubl\Service\Format\CommerceNumberBE;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CommerceNumberBETest extends TestCase
{
    #[DataProvider('validCommerceNumberProvider')]
    public function testValidCommerceNumberFormats(string $identifier, string $expected): void
    {
        $this->assertEquals($expected, (new CommerceNumberBE())->format($identifier));
    }

    public static function validCommerceNumberProvider(): array
    {
        return [
            'former format' => ['600000032', '0600000032'],
            'current format' => ['0123456749', '0123456749'],
            'human readable format with spaces' => ['0456 789 034', '0456789034'],
            'human readable format using dots' => ['1234 567 894', '1234567894'],
        ];
    }

    #[DataProvider('invalidCommerceNumberProvider')]
    public function testInvalidCommerceNumberFormats(string $identifier): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new CommerceNumberBE())->format($identifier);
    }

    public static function invalidCommerceNumberProvider(): array
    {
        return [
            'non-numeric identifier' => ['invalid123'],
            'too few digits' => ['12345678'],
            'invalid character' => ['0123-123-123'],
            'does not start with 0 or 1' => ['2233123123'],
            'mod 97 check fails' => ['0123123142'],
        ];
    }
}
