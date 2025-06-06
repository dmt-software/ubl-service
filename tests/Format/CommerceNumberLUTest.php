<?php

namespace DMT\Test\Ubl\Service\Format;

use DMT\Ubl\Service\Format\CommerceNumberLU;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CommerceNumberLUTest extends TestCase
{
    #[DataProvider('validCommerceNumberProvider')]
    public function testValidCommerceNumberFormats(string $identifier, string $expected): void
    {
        $this->assertSame($expected, (new CommerceNumberLU())->format($identifier));
    }

    public static function validCommerceNumberProvider(): array
    {
        return [
            'valid number' => ['1990022123482', '1990022123482'],
            'trim whitespace' => ['1990022123482 ', '1990022123482']
        ];
    }

    #[DataProvider('invalidCommerceNumberProvider')]
    public function testInvalidCommerceNumberFormats(string $identifier): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new CommerceNumberLU())->format($identifier);
    }

    public static function invalidCommerceNumberProvider(): array
    {
        return [
            'non-numeric characters' => ['X9873451231'],
            'too short number' => ['199056798732'],
            'too long number' => ['20205678912333'],
            'contains spaces' => ['123 45 678 222 531'],
            'registered in the future' => ['31250311303'],
            'non-existing birth date' => ['1975133199833'],
            'deceased sole propriety' => ['1895031487612'],
        ];
    }
}
