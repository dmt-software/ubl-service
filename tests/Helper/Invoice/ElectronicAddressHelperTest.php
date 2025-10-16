<?php

namespace DMT\Test\Ubl\Service\Helper\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\CompanyId;
use DMT\Ubl\Service\Entity\Invoice\Type\ElectronicAddressType;
use DMT\Ubl\Service\Entity\Invoice\Type\EndpointId;
use DMT\Ubl\Service\Helper\Invoice\ElectronicAddressHelper;
use DMT\Ubl\Service\List\ElectronicAddressScheme;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ElectronicAddressHelperTest extends TestCase
{
    #[DataProvider(methodName: 'provideElectronicAddress')]
    public function testFetchFromValue(string $addressType, mixed $value, null|ElectronicAddressType $expected): void
    {
        $this->assertEquals($expected, ElectronicAddressHelper::fetchFromValue($value, $addressType));
    }

    public static function provideElectronicAddress(): iterable
    {
        $endpointId = new EndpointId();
        $endpointId->id = '01000332';
        $endpointId->schemeId = ElectronicAddressScheme::NLCommerceNumber;

        yield 'from object' => [EndpointId::class, (object)['id' => '01000332', 'schemeId' => '0106'], $endpointId];
        yield 'set from self' => [EndpointId::class, $endpointId, $endpointId];

        $companyId = new CompanyId();
        $companyId->id = 'BE1938736728';

        yield 'from string' => [CompanyId::class, 'BE1938736728', $companyId];
        yield 'not set' => [CompanyId::class, null, null];
    }
}
