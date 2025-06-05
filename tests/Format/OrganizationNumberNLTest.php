<?php

namespace DMT\Test\Ubl\Service\Format;

use DMT\Ubl\Service\Format\OrganizationNumberNL;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OrganizationNumberNLTest extends TestCase
{
    #[DataProvider('validOrganizationNumberProvider')]
    public function testValidOrganizationNumberFormats(string $identifier, string $expected): void
    {
        $this->assertSame($expected, (new OrganizationNumberNL())->format($identifier));
    }

    public static function validOrganizationNumberProvider(): array
    {
        return [
            'trim whitespace' => [' 00345678901234567000 ', '00345678901234567000'],
            'valid number' => ['00345678901234560000', '00345678901234560000'],
        ];
    }

    #[DataProvider('invalidOrganizationNumberProvider')]
    public function testInvalidOrganizationNumberFormats(string $identifier): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new OrganizationNumberNL())->format($identifier);
    }

    public static function invalidOrganizationNumberProvider(): array
    {
        return [
            'non numeric' => ['abcd12345678efghijkl'],
            'number too short' => ['12345'],
            'number too long' => ['12345678901234567890123'],
        ];
    }
}