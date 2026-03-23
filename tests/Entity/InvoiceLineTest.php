<?php

namespace DMT\Test\Ubl\Service\Entity;

use DMT\Ubl\Service\Entity\CommonAggregateComponents\AllowanceCharge;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\InvoiceLine;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Item;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Price;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\TaxTotal;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Amount;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Quantity;
use DMT\Ubl\Service\Entity\Versions;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class InvoiceLineTest extends TestCase
{
    public function testSerializeVersionNlcius(): void
    {
        $invoiceLine = $this->getInvoiceLine();

        $context = SerializationContext::create()->setVersion(Versions::VERSION_NLCIUS);

        $xml = simplexml_load_string($this->getSerializer()->serialize($invoiceLine, 'xml', $context));

        $this->assertEquals('InvoiceLine', $xml->getName());
        $this->assertEquals(
            $invoiceLine->id,
            strval($xml->xpath('*[local-name()="ID"]')[0]),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ID"]')[0]->getNamespaces(),
        );
        $this->assertStringStartsWith(
            strval($xml->xpath('*[local-name()="LineExtensionAmount"]')[0]),
            strval($invoiceLine->lineExtensionAmount)
        );
        $this->assertStringEndsWith(
            strval($xml->xpath('*[local-name()="LineExtensionAmount"]/@currencyID')[0]),
            $invoiceLine->lineExtensionAmount->currencyId
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="AllowanceCharge"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="LineExtensionAmount"]')[0]->getNamespaces(),
        );
        $this->assertEmpty($xml->xpath('*[local-name()="TaxTotal"]'));
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="Item"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="Price"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->getDocNamespaces()
        );
    }

    public function testSerializeVersion10200(): void
    {
        $invoiceLine = $this->getInvoiceLine();

        $context = SerializationContext::create()->setVersion(Versions::VERSION_1_2);

        $xml = simplexml_load_string($this->getSerializer()->serialize($invoiceLine, 'xml', $context));

        $this->assertEquals('InvoiceLine', $xml->getName());
        $this->assertEquals(
            $invoiceLine->id,
            strval($xml->xpath('*[local-name()="ID"]')[0]),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ID"]')[0]->getNamespaces(),
        );
        $this->assertStringStartsWith(
            strval($xml->xpath('*[local-name()="LineExtensionAmount"]')[0]),
            strval($invoiceLine->lineExtensionAmount)
        );
        $this->assertStringEndsWith(
            strval($xml->xpath('*[local-name()="LineExtensionAmount"]/@currencyID')[0]),
            $invoiceLine->lineExtensionAmount->currencyId
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="LineExtensionAmount"]')[0]->getNamespaces(),
        );
        $this->assertEmpty($xml->xpath('*[local-name()="AllowanceCharge"]'));
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="TaxTotal"]')[0]->getNamespaces()
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="Item"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="Price"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->getDocNamespaces()
        );
    }

    public function getInvoiceLine(): InvoiceLine
    {
        $invoiceLine = new InvoiceLine();
        $invoiceLine->id = '1345';
        $invoiceLine->invoicedQuantity = new Quantity();
        $invoiceLine->invoicedQuantity->quantity = 3;
        $invoiceLine->lineExtensionAmount = new Amount();
        $invoiceLine->lineExtensionAmount->amount = 45.00;
        $invoiceLine->lineExtensionAmount->currencyId = 'EUR';
        $invoiceLine->allowanceCharge = [new AllowanceCharge()];
        $invoiceLine->allowanceCharge[0]->amount = new Amount();
        $invoiceLine->allowanceCharge[0]->amount->amount = 3.00;
        $invoiceLine->taxTotal = new TaxTotal();
        $invoiceLine->taxTotal->taxAmount = new Amount();
        $invoiceLine->taxTotal->taxAmount->amount = 0.00;
        $invoiceLine->item = new Item();
        $invoiceLine->item->name = 'Sku 1345';
        $invoiceLine->price = new Price();
        $invoiceLine->price->priceAmount = new Amount();
        $invoiceLine->price->priceAmount->amount = 15.00;

        return $invoiceLine;
    }

    public function getSerializer(): Serializer
    {
        $builder = SerializerBuilder::create();

        return $builder->build();
    }
}
