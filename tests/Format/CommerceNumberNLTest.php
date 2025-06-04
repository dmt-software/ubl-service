<?php

namespace DMT\Test\Ubl\Service\Format;

use DMT\Ubl\Service\Format\CommerceNumberNL;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CommerceNumberNLTest extends TestCase
{
    #[DataProvider('validCommerceNumberProvider')]
    public function testValidCommerceNumberFormats(string $identifier, string $expected): void
    {
        $this->assertSame($expected, (new CommerceNumberNL())->format($identifier));
    }


    public static function validCommerceNumberProvider(): array
    {
        return [
            'valid number' => ['12345678', '12345678'],
            'trims whitespace' => [' 12345678 ', '12345678'],
        ];
    }

    #[DataProvider('invalidCommerceNumberProvider')]
    public function testInvalidCommerceNumberFormats(string $identifier): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new CommerceNumberNL())->format($identifier);
    }

    public static function invalidCommerceNumberProvider(): array
    {
        return [
            'non-numeric characters' => ['A124434X'],
            'too short number' => ['1234567'],
            'too long number' => ['123456789'],
            'contains spaces' => ['123 45 678'],
        ];
    }
}