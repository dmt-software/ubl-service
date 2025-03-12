<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\LegalMonetaryTotal;
use DMT\Ubl\Service\Entity\Invoice\Type\AllowanceTotalAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\ChargeTotalAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\LineExtensionAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\PayableAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\PayableRoundingAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\PrepaidAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\TaxExclusiveAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\TaxInclusiveAmount;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class LegalMonetaryTotalTest extends TestCase
{
    public function testSerialize(): void
    {
        $legalMonetaryTotal = new LegalMonetaryTotal();
        $legalMonetaryTotal->allowanceTotalAmount = new AllowanceTotalAmount();
        $legalMonetaryTotal->allowanceTotalAmount->amount = 123.55;
        $legalMonetaryTotal->chargeTotalAmount = new ChargeTotalAmount();
        $legalMonetaryTotal->chargeTotalAmount->amount = 3.95;
        $legalMonetaryTotal->lineExtensionAmount = new LineExtensionAmount();
        $legalMonetaryTotal->lineExtensionAmount->amount = 1424.05;
        $legalMonetaryTotal->payableAmount = new PayableAmount();
        $legalMonetaryTotal->payableAmount->amount = 1123.66;
        $legalMonetaryTotal->payableRoundingAmount = new PayableRoundingAmount();
        $legalMonetaryTotal->payableRoundingAmount->amount = 0.02;
        $legalMonetaryTotal->prepaidAmount = new PrepaidAmount();
        $legalMonetaryTotal->prepaidAmount->amount = 985.45;
        $legalMonetaryTotal->taxExclusiveAmount = new TaxExclusiveAmount();
        $legalMonetaryTotal->taxExclusiveAmount->amount = 123.45;
        $legalMonetaryTotal->taxInclusiveAmount = new TaxInclusiveAmount();
        $legalMonetaryTotal->taxInclusiveAmount->amount = 177.23;



        $xml = simplexml_load_string($this->getSerializer()->serialize($legalMonetaryTotal, 'xml'));

        $this->assertEquals('LegalMonetaryTotal', $xml->getName());
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="AllowanceTotalAmount"]')[0]->getNamespaces()
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ChargeTotalAmount"]')[0]->getNamespaces()
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="LineExtensionAmount"]')[0]->getNamespaces()
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="PayableAmount"]')[0]->getNamespaces()
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="PayableRoundingAmount"]')[0]->getNamespaces()
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="PrepaidAmount"]')[0]->getNamespaces()
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="TaxExclusiveAmount"]')[0]->getNamespaces()
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="TaxInclusiveAmount"]')[0]->getNamespaces()
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->getDocNamespaces()
        );
    }


    public function getSerializer(): Serializer
    {
        $builder = SerializerBuilder::create();

        return $builder->build();
    }
}
