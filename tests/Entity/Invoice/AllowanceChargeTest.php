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
    public function testSerializeAllowance(): void
    {
        $allowance = new AllowanceCharge();
        $allowance->chargeIndicator = false;
        $allowance->allowanceChargeReasonCode = 'D10';
        $allowance->allowanceChargeReason = '10% discount';
        $allowance->taxCategory = new TaxCategory();
        $allowance->taxCategory->percent = 10;

        $xml = simplexml_load_string($this->getSerializer()->serialize($allowance, 'xml'));

        $this->assertEquals('AllowanceCharge', $xml->getName());
        $this->assertEquals(
            'false',
            strval($xml->xpath('*[local-name()="ChargeIndicator"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ChargeIndicator"]')[0]->getNamespaces()
        );
        $this->assertEquals(
            $allowance->allowanceChargeReasonCode,
            strval($xml->xpath('*[local-name()="AllowanceChargeReasonCode"]')[0])
        );
        $this->assertEquals(
            $allowance->allowanceChargeReason,
            strval($xml->xpath('*[local-name()="AllowanceChargeReason"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="AllowanceChargeReason"]')[0]->getNamespaces()
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="TaxCategory"]')[0]->getNamespaces()
        );
        $this->assertEquals(
            $allowance->taxCategory->percent,
            intval($xml->xpath('*[local-name()="TaxCategory"]/*[local-name()="Percent"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->getDocNamespaces()
        );
    }

    public function testSerializeCharge(): void
    {
        $charge = new AllowanceCharge();
        $charge->chargeIndicator = true;
        $charge->allowanceChargeReason = 'Drop shipment';
        $charge->amount = new Amount();
        $charge->amount->amount = 10.0;
        $charge->amount->currencyId = 'EUR';

        $xml = simplexml_load_string($this->getSerializer()->serialize($charge, 'xml'));

        $this->assertEquals('AllowanceCharge', $xml->getName());
        $this->assertEquals(
            'true',
            strval($xml->xpath('*[local-name()="ChargeIndicator"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ChargeIndicator"]')[0]->getNamespaces()
        );
        $this->assertEquals(
            $charge->allowanceChargeReason,
            strval($xml->xpath('*[local-name()="AllowanceChargeReason"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="AllowanceChargeReason"]')[0]->getNamespaces()
        );
        $this->assertEquals(
            $charge->amount->amount,
            floatval($xml->xpath('*[local-name()="Amount"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="Amount"]')[0]->getNamespaces()
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->getDocNamespaces()
        );
    }

    protected function getSerializer(): Serializer
    {
        $builder = SerializerBuilder::create();

        return $builder->build();
    }
}
