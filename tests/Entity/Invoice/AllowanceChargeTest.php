<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\AllowanceCharge;
use DMT\Ubl\Service\Entity\Invoice\TaxCategory;
use DMT\Ubl\Service\Entity\Invoice\Type\Amount;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class AllowanceChargeTest extends TestCase
{
    public function testAllowance(): void
    {
        $allowance = new AllowanceCharge();
        $allowance->chargeIndicator = false;
        $allowance->taxCategory = new TaxCategory();
        $allowance->taxCategory->percent = 10;

        $xml = simplexml_load_string($this->getSerializer()->serialize($allowance, 'xml'));

        $this->assertEquals('false', $xml->xpath('*[local-name()="ChargeIndicator"]')[0]);
        $this->assertEquals(
            $allowance->taxCategory->percent,
            intval($xml->xpath('*[local-name()="TaxCategory"]/*[local-name()="Percent"]')[0])
        );
    }

    public function testCharge(): void
    {
        $charge = new AllowanceCharge();
        $charge->amount = new Amount();
        $charge->chargeIndicator = true;
        $charge->allowanceChargeReason = 'Drop shipment';
        $charge->amount->amount = 100;
        $charge->amount->currencyId = 'EUR';

        $xml = simplexml_load_string($this->getSerializer()->serialize($charge, 'xml'));

        $this->assertEquals('true', $xml->xpath('*[local-name()="ChargeIndicator"]')[0]);
        $this->assertEquals($charge->allowanceChargeReason, $xml->xpath('*[local-name()="AllowanceChargeReason"]')[0]);
        $this->assertEquals($charge->amount->amount, strval($xml->xpath('*[local-name()="Amount"]')[0]));
        $this->assertEquals($charge->amount->currencyId, $xml->xpath('*[local-name()="Amount"]/@currencyId')[0]);
    }

    protected function getSerializer(): Serializer
    {
        $builder = SerializerBuilder::create();

        return $builder->build();
    }
}
