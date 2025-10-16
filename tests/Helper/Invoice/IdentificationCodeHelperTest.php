<?php

namespace DMT\Test\Ubl\Service\Helper\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\IdentificationCode;
use DMT\Ubl\Service\Helper\Invoice\IdentificationCodeHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IdentificationCodeHelperTest extends TestCase
{
    #[DataProvider(methodName: 'provideIdentificationCode')]
    public function testFetchFromValue(mixed $value, IdentificationCode $expected): void
    {
        $this->assertSame($expected, IdentificationCodeHelper::fetchFromValue($value));
    }

    public static function provideIdentificationCode(): iterable
    {
        $identificationCode = new IdentificationCode();
        $identificationCode->code = 'NL';

        yield 'from string' => ['NL', $identificationCode];
        yield 'from object' => [(object)['code' => 'NL'], $identificationCode];
        yield 'set from self' => [$identificationCode, $identificationCode];
    }
}
