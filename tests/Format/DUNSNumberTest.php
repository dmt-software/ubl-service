<?php

namespace DMT\Test\Ubl\Service\Format;

use DMT\Ubl\Service\Format\DUNSNumber;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DUNSNumberTest extends TestCase
{
    #[DataProvider('validDUNSNumberProvider')]
    public function testValidDUNSNumberFormats(string $identifier, string $expected): void
    {
        $this->assertSame($expected, (new DUNSNumber())->format($identifier));
    }

    public static function validDUNSNumberProvider(): array
    {
        return [
            'human readable form' => ['12-345-6789', '123456789'],
            'plain' => ['123456789', '123456789'],
        ];
    }

    #[DataProvider('invalidDUNSNumberProvider')]
    public function testInvalidDUNSNumberFormats(string $identifier): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new DUNSNumber())->format($identifier);
    }

    public static function invalidDUNSNumberProvider(): array
    {
        return [
            'contains non-numeric characters' => ['ABC-123-567'],
            'too short' => ['12345678'],
            'too long' => ['1234567890'],
            'incorrectly formatted' => ['123-345-678'],
        ];
    }
}