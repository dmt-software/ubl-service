<?php

namespace DMT\Test\Ubl\Service\Helper\Invoice;

use DateTime;
use DMT\Ubl\Service\Helper\Invoice\DateTypeHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DateTypeHelperTest extends TestCase
{
    #[DataProvider(methodName: 'provideDate')]
    public function testFetchFromValue(mixed $value, null|DateTime $expected): void
    {
        if ($expected === null) {
            $this->assertNull(DateTypeHelper::fetchFromValue($value));
        } else {
            $secondsExpired = $expected->getTimestamp() - DateTypeHelper::fetchFromValue($value)->getTimestamp();
            $this->assertLessThanOrEqual(1, $secondsExpired);
        }
    }

    public static function provideDate(): iterable
    {
        return [
            'from string' => ['2025-09-25', new DateTime('2025-09-25')],
            'from now' => ['now', new DateTime('now')],
            'not set from null' => [null, null],
            'set from empty string' => ['', null],
            'set from self' => [new DateTime('2024-10-01'), new DateTime('2024-10-01')],
            'not set from malformed date' => ['2025-13-11', null],
        ];
    }
}
