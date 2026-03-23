<?php

namespace DMT\Test\Ubl\Service\Entity\Components;

use DMT\Ubl\Service\Entity\CommonAggregateComponents\LegalMonetaryTotal;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Amount;
use DMT\Ubl\Service\Event\AmountCurrencyEventSubscriber;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class LegalMonetaryTotalTest extends TestCase
{
    public function testSerialize(): void
    {
        $legalMonetaryTotal = new LegalMonetaryTotal();
        $legalMonetaryTotal->allowanceTotalAmount = new Amount();
        $legalMonetaryTotal->allowanceTotalAmount->amount = 123.55;
        $legalMonetaryTotal->chargeTotalAmount = new Amount();
        $legalMonetaryTotal->chargeTotalAmount->amount = 3.95;
        $legalMonetaryTotal->lineExtensionAmount = new Amount();
        $legalMonetaryTotal->lineExtensionAmount->amount = 1424.05;
        $legalMonetaryTotal->payableAmount = new Amount();
        $legalMonetaryTotal->payableAmount->amount = 1123.66;
        $legalMonetaryTotal->payableRoundingAmount = new Amount();
        $legalMonetaryTotal->payableRoundingAmount->amount = 0.02;
        $legalMonetaryTotal->prepaidAmount = new Amount();
        $legalMonetaryTotal->prepaidAmount->amount = 985.45;
        $legalMonetaryTotal->taxExclusiveAmount = new Amount();
        $legalMonetaryTotal->taxExclusiveAmount->amount = 123.45;
        $legalMonetaryTotal->taxInclusiveAmount = new Amount();
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
        $builder->configureListeners(function (EventDispatcher $dispatcher) {
            $dispatcher->addSubscriber(new AmountCurrencyEventSubscriber());
        });

        return $builder->build();
    }
}
