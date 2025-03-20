<?php

namespace DMT\Test\Ubl\Service\Entity;

use DateTime;
use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\AccountingCustomerParty;
use DMT\Ubl\Service\Entity\Invoice\AccountingSupplierParty;
use DMT\Ubl\Service\Entity\Invoice\Address;
use DMT\Ubl\Service\Entity\Invoice\AllowanceCharge;
use DMT\Ubl\Service\Entity\Invoice\Delivery;
use DMT\Ubl\Service\Entity\Invoice\DeliveryLocation;
use DMT\Ubl\Service\Entity\Invoice\InvoicePeriod;
use DMT\Ubl\Service\Entity\Invoice\LegalMonetaryTotal;
use DMT\Ubl\Service\Entity\Invoice\OrderReference;
use DMT\Ubl\Service\Entity\Invoice\Party;
use DMT\Ubl\Service\Entity\Invoice\PartyName;
use DMT\Ubl\Service\Entity\Invoice\TaxTotal;
use DMT\Ubl\Service\Entity\Invoice\Type\DocumentCurrencyCode;
use DMT\Ubl\Service\Entity\Invoice\Type\EndpointId;
use DMT\Ubl\Service\Entity\Invoice\Type\InvoiceTypeCode;
use DMT\Ubl\Service\Entity\Invoice\Type\PayableAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\TaxAmount;
use DMT\Ubl\Service\Entity\InvoiceLine;
use DMT\Ubl\Service\Event\InvoiceCustomizationEventSubscriber;
use DMT\Ubl\Service\List\InvoiceType;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class InvoiceTest extends TestCase
{
    public function testSerializeVersion20000(): void
    {
        $invoice = $this->getInvoice();

        $context = SerializationContext::create()->setVersion(Invoice::VERSION_2_0);

        $xml = simplexml_load_string($this->getSerializer()->serialize($invoice, 'xml', $context));

        $this->assertEquals('Invoice', $xml->getName());
        $this->assertEmpty($xml->xpath('*[local-name()="UBLVersionID"]'));
        $this->assertEquals(
            InvoiceCustomizationEventSubscriber::CUSTOMIZATION_DEFAULT,
            strval($xml->xpath('*[local-name()="CustomizationID"]')[0]),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="CustomizationID"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0',
            strval($xml->xpath('*[local-name()="ProfileID"]')[0]),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ProfileID"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->id,
            strval($xml->xpath('*[local-name()="ID"]')[0]),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ID"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->issueDate->format('Y-m-d'),
            strval($xml->xpath('*[local-name()="IssueDate"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="IssueDate"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->invoiceTypeCode,
            strval($xml->xpath('*[local-name()="InvoiceTypeCode"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="InvoiceTypeCode"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->taxPointDate->format('Y-m-d'),
            strval($xml->xpath('*[local-name()="TaxPointDate"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="TaxPointDate"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->documentCurrencyCode,
            strval($xml->xpath('*[local-name()="DocumentCurrencyCode"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="DocumentCurrencyCode"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->accountingCost,
            strval($xml->xpath('*[local-name()="AccountingCost"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="AccountingCost"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="InvoicePeriod"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="OrderReference"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="AccountingSupplierParty"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="AccountingCustomerParty"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="Delivery"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="AllowanceCharge"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="TaxTotal"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="LegalMonetaryTotal"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="InvoiceLine"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->getDocNamespaces()
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->getDocNamespaces()
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
            $xml->getDocNamespaces()
        );
    }

    public function testSerializeVersionNlcius(): void
    {
        $invoice = $this->getInvoice();

        $context = SerializationContext::create()->setVersion(Invoice::VERSION_NLCIUS);

        $xml = simplexml_load_string($this->getSerializer()->serialize($invoice, 'xml', $context));

        $this->assertEquals('Invoice', $xml->getName());
        $this->assertEmpty($xml->xpath('*[local-name()="UBLVersionID"]'));
        $this->assertEquals(
            InvoiceCustomizationEventSubscriber::CUSTOMIZATION_2_0_NLCIUS,
            strval($xml->xpath('*[local-name()="CustomizationID"]')[0]),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="CustomizationID"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0',
            strval($xml->xpath('*[local-name()="ProfileID"]')[0]),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ProfileID"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->id,
            strval($xml->xpath('*[local-name()="ID"]')[0]),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ID"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->issueDate->format('Y-m-d'),
            strval($xml->xpath('*[local-name()="IssueDate"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="IssueDate"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->invoiceTypeCode,
            strval($xml->xpath('*[local-name()="InvoiceTypeCode"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="InvoiceTypeCode"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->taxPointDate->format('Y-m-d'),
            strval($xml->xpath('*[local-name()="TaxPointDate"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="TaxPointDate"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->documentCurrencyCode,
            strval($xml->xpath('*[local-name()="DocumentCurrencyCode"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="DocumentCurrencyCode"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->accountingCost,
            strval($xml->xpath('*[local-name()="AccountingCost"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="AccountingCost"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="InvoicePeriod"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="OrderReference"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="AccountingSupplierParty"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="AccountingCustomerParty"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="Delivery"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="AllowanceCharge"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="TaxTotal"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="LegalMonetaryTotal"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="InvoiceLine"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->getDocNamespaces()
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->getDocNamespaces()
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
            $xml->getDocNamespaces()
        );
    }

    public function testSerializeVersion10200(): void
    {
        $invoice = $this->getInvoice();

        $context = SerializationContext::create()->setVersion(Invoice::VERSION_1_2);

        $xml = simplexml_load_string($this->getSerializer()->serialize($invoice, 'xml', $context));

        $this->assertEquals('Invoice', $xml->getName());
        $this->assertEquals('2.1', strval($xml->xpath('*[local-name()="UBLVersionID"]')[0]));
        $this->assertEquals(
            InvoiceCustomizationEventSubscriber::CUSTOMIZATION_1_2,
            strval($xml->xpath('*[local-name()="CustomizationID"]')[0]),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="CustomizationID"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            'urn:www.cenbii.eu:profile:bii04:ver1.0',
            strval($xml->xpath('*[local-name()="ProfileID"]')[0]),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ProfileID"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->id,
            strval($xml->xpath('*[local-name()="ID"]')[0]),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ID"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->issueDate->format('Y-m-d'),
            strval($xml->xpath('*[local-name()="IssueDate"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="IssueDate"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->invoiceTypeCode,
            strval($xml->xpath('*[local-name()="InvoiceTypeCode"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="InvoiceTypeCode"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->taxPointDate->format('Y-m-d'),
            strval($xml->xpath('*[local-name()="TaxPointDate"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="TaxPointDate"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->documentCurrencyCode,
            strval($xml->xpath('*[local-name()="DocumentCurrencyCode"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="DocumentCurrencyCode"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->accountingCost,
            strval($xml->xpath('*[local-name()="AccountingCost"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="AccountingCost"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="InvoicePeriod"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="OrderReference"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="AccountingSupplierParty"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="AccountingCustomerParty"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="Delivery"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="AllowanceCharge"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="TaxTotal"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="LegalMonetaryTotal"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="InvoiceLine"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->getDocNamespaces()
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->getDocNamespaces()
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
            $xml->getDocNamespaces()
        );
    }

    public function testSerializeVersion10100(): void
    {
        $invoice = $this->getInvoice();

        $context = SerializationContext::create()->setVersion(Invoice::VERSION_1_1);

        $xml = simplexml_load_string($this->getSerializer()->serialize($invoice, 'xml', $context));

        $this->assertEquals('Invoice', $xml->getName());
        $this->assertEquals('2.1', strval($xml->xpath('*[local-name()="UBLVersionID"]')[0]));
        $this->assertEquals(
            InvoiceCustomizationEventSubscriber::CUSTOMIZATION_1_1,
            strval($xml->xpath('*[local-name()="CustomizationID"]')[0]),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="CustomizationID"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            'urn:www.cenbii.eu:profile:bii04:ver1.0',
            strval($xml->xpath('*[local-name()="ProfileID"]')[0]),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ProfileID"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->id,
            strval($xml->xpath('*[local-name()="ID"]')[0]),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ID"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->issueDate->format('Y-m-d'),
            strval($xml->xpath('*[local-name()="IssueDate"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="IssueDate"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->invoiceTypeCode,
            strval($xml->xpath('*[local-name()="InvoiceTypeCode"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="InvoiceTypeCode"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->taxPointDate->format('Y-m-d'),
            strval($xml->xpath('*[local-name()="TaxPointDate"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="TaxPointDate"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->documentCurrencyCode,
            strval($xml->xpath('*[local-name()="DocumentCurrencyCode"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="DocumentCurrencyCode"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->accountingCost,
            strval($xml->xpath('*[local-name()="AccountingCost"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="InvoicePeriod"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="OrderReference"]')[0]->getNamespaces()
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="AccountingSupplierParty"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="AccountingCustomerParty"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="Delivery"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="AllowanceCharge"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="TaxTotal"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="LegalMonetaryTotal"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="InvoiceLine"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->getDocNamespaces()
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->getDocNamespaces()
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
            $xml->getDocNamespaces()
        );
    }

    public function testSerializeVersion10000(): void
    {
        $invoice = $this->getInvoice();

        $context = SerializationContext::create()->setVersion(Invoice::VERSION_1_0);

        $xml = simplexml_load_string($this->getSerializer()->serialize($invoice, 'xml', $context));

        $this->assertEquals('Invoice', $xml->getName());
        $this->assertEquals('2.0', strval($xml->xpath('*[local-name()="UBLVersionID"]')[0]));
        $this->assertEquals(
            InvoiceCustomizationEventSubscriber::CUSTOMIZATION_1_0,
            strval($xml->xpath('*[local-name()="CustomizationID"]')[0]),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="CustomizationID"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            'urn:www.cenbii.eu:profile:bii04:ver1.0',
            strval($xml->xpath('*[local-name()="ProfileID"]')[0]),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ProfileID"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->id,
            strval($xml->xpath('*[local-name()="ID"]')[0]),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ID"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->issueDate->format('Y-m-d'),
            strval($xml->xpath('*[local-name()="IssueDate"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="IssueDate"]')[0]->getNamespaces(),
        );
        $this->assertEquals(
            $invoice->invoiceTypeCode,
            strval($xml->xpath('*[local-name()="InvoiceTypeCode"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="InvoiceTypeCode"]')[0]->getNamespaces(),
        );
        $this->assertEmpty(strval($xml->xpath('*[local-name()="TaxPointDate"]')[0]));
        $this->assertEquals(
            $invoice->documentCurrencyCode,
            strval($xml->xpath('*[local-name()="DocumentCurrencyCode"]')[0])
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="DocumentCurrencyCode"]')[0]->getNamespaces(),
        );
        $this->assertEmpty(strval($xml->xpath('*[local-name()="AccountingCost"]')[0]));
        $this->assertEmpty($xml->xpath('*[local-name()="InvoicePeriod"]'));
        $this->assertEmpty($xml->xpath('*[local-name()="OrderReference"]'));
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="AccountingSupplierParty"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="AccountingCustomerParty"]')[0]->getNamespaces(),
        );
        $this->assertEmpty($xml->xpath('*[local-name()="Delivery"]'));
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="AllowanceCharge"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="TaxTotal"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="LegalMonetaryTotal"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="InvoiceLine"]')[0]->getNamespaces(),
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->getDocNamespaces()
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->getDocNamespaces()
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
            $xml->getDocNamespaces()
        );
    }

    public function getInvoice(): Invoice
    {
        $invoice = new Invoice();
        $invoice->id = '1442356';
        $invoice->issueDate = new DateTime('2025-03-08');
        $invoice->invoiceTypeCode = new InvoiceTypeCode();
        $invoice->invoiceTypeCode->code = InvoiceType::Normal;
        $invoice->taxPointDate = new DateTime('2025-03-08');
        $invoice->documentCurrencyCode = new DocumentCurrencyCode();
        $invoice->documentCurrencyCode->code = 'EUR';
        $invoice->accountingCost = 'F1235523';
        $invoice->invoicePeriod = new InvoicePeriod();
        $invoice->invoicePeriod->startDate = new DateTime('2025-03-08');
        $invoice->invoicePeriod->endDate = new DateTime('2025-04-01');
        $invoice->orderReference = new OrderReference();
        $invoice->orderReference->id = 'ORD99872';
        $invoice->accountingSupplierParty = new AccountingSupplierParty();
        $invoice->accountingSupplierParty->party = new Party();
        $invoice->accountingSupplierParty->party->endpointId = new EndpointId();
        $invoice->accountingSupplierParty->party->endpointId->id = '9863308623521';
        $invoice->accountingCustomerParty = new AccountingCustomerParty();
        $invoice->accountingCustomerParty->party = new Party();
        $invoice->accountingCustomerParty->party->partyName = new PartyName();
        $invoice->accountingCustomerParty->party->partyName->name = 'Holding BV';
        $invoice->delivery = new Delivery();
        $invoice->delivery->deliveryLocation = new DeliveryLocation();
        $invoice->delivery->deliveryLocation->address = new Address();
        $invoice->delivery->deliveryLocation->address->cityName = 'City';
        $invoice->allowanceCharge = [new AllowanceCharge()];
        $invoice->allowanceCharge[0]->chargeIndicator = true;
        $invoice->taxTotal = new TaxTotal();
        $invoice->taxTotal->taxAmount = new TaxAmount();
        $invoice->taxTotal->taxAmount->amount = 482.99;
        $invoice->legalMonetaryTotal = new LegalMonetaryTotal();
        $invoice->legalMonetaryTotal->payableAmount = new PayableAmount();
        $invoice->legalMonetaryTotal->payableAmount->amount = 1443.77;
        $invoice->invoiceLine = [new InvoiceLine()];
        $invoice->invoiceLine[0]->id = '1554300687';

        return $invoice;
    }

    public function getSerializer(): Serializer
    {
        $builder = SerializerBuilder::create();
        $builder->enableEnumSupport();
        $builder->configureListeners(function (EventDispatcher $dispatcher) {
            $dispatcher->addSubscriber(new InvoiceCustomizationEventSubscriber());
        });

        return $builder->build();
    }
}
