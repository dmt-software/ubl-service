<?php

namespace DMT\Test\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\ClassifiedTaxCategory;
use DMT\Ubl\Service\Entity\Invoice\Item;
use DMT\Ubl\Service\Entity\Invoice\TaxScheme;
use DMT\Ubl\Service\Entity\Invoice\TaxTotal;
use DMT\Ubl\Service\Entity\Invoice\Type\Id;
use DMT\Ubl\Service\Entity\Invoice\Type\LineExtensionAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\TaxAmount;
use DMT\Ubl\Service\Entity\InvoiceLine;
use DMT\Ubl\Service\Event\TaxCategoryEventSubscriber;
use DMT\Ubl\Service\Helper\Invoice\AmountHelper;
use DMT\Ubl\Service\Helper\Invoice\ElectronicAddressHelper;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\SerializationContext;
use PHPUnit\Framework\TestCase;

class TaxCategoryEventSubscriberTest extends TestCase
{
    public function testSetClassifiedTaxCategories(): void
    {
        $invoice = new Invoice();
        $invoice->invoiceLine = [0 => new InvoiceLine()];
        $invoice->invoiceLine[0]->item = new Item();
        $invoice->invoiceLine[0]->item->classifiedTaxCategory = new ClassifiedTaxCategory();
        $invoice->invoiceLine[0]->item->classifiedTaxCategory->percent = 21;

        $event = new PreSerializeEvent(
            SerializationContext::create(),
            $invoice,
            ['name' => Invoice::class]
        );

        $subscriber = new TaxCategoryEventSubscriber();
        $subscriber->setClassifiedTaxCategories($event);

        $this->assertSame('S', $invoice->invoiceLine[0]->item->classifiedTaxCategory->id->id);
        $this->assertSame('VAT', $invoice->invoiceLine[0]->item->classifiedTaxCategory->taxScheme->id->id);
        $this->assertInstanceOf(TaxTotal::class, $invoice->invoiceLine[0]->taxTotal);
    }

    public function testSetTaxTotalForVersion1(): void
    {
        $invoice = new Invoice();
        $invoice->invoiceLine = [0 => new InvoiceLine()];
        $invoice->invoiceLine[0]->lineExtensionAmount = AmountHelper::fetchFromValue(100.00, LineExtensionAmount::class);
        $invoice->invoiceLine[0]->taxTotal = new TaxTotal();
        $invoice->invoiceLine[0]->taxTotal->taxAmount = AmountHelper::fetchFromValue(6.00, TaxAmount::class);

        $event = new PreSerializeEvent(
            SerializationContext::create()->setVersion(Invoice::VERSION_1_0),
            $invoice,
            ['name' => Invoice::class]
        );

        $subscriber = new TaxCategoryEventSubscriber();
        $subscriber->setTaxTotal($event);

        $this->assertInstanceOf(TaxTotal::class, $invoice->taxTotal);
        $this->assertEquals(6.00, $invoice->taxTotal->taxAmount->amount);
        $this->assertNull($invoice->taxTotal->taxSubtotal);
    }

    public function testSetTaxTotalForVersion2(): void
    {
        $invoice = new Invoice();
        $invoice->invoiceLine = [0 => new InvoiceLine(), 1 => new InvoiceLine(), 2 => new InvoiceLine()];
        $invoice->invoiceLine[0]->lineExtensionAmount = AmountHelper::fetchFromValue(45.00, LineExtensionAmount::class);
        $invoice->invoiceLine[0]->item = new Item();
        $invoice->invoiceLine[0]->item->classifiedTaxCategory = new ClassifiedTaxCategory();
        $invoice->invoiceLine[0]->item->classifiedTaxCategory->id =
            ElectronicAddressHelper::fetchFromValue('S', Id::class);
        $invoice->invoiceLine[0]->item->classifiedTaxCategory->taxScheme = new TaxScheme();
        $invoice->invoiceLine[0]->item->classifiedTaxCategory->taxScheme->id =
            ElectronicAddressHelper::fetchFromValue('VAT', Id::class);
        $invoice->invoiceLine[0]->item->classifiedTaxCategory->percent = 21;

        $invoice->invoiceLine[1]->lineExtensionAmount = AmountHelper::fetchFromValue(6.00, LineExtensionAmount::class);
        $invoice->invoiceLine[1]->item = new Item();
        $invoice->invoiceLine[1]->item->classifiedTaxCategory = new ClassifiedTaxCategory();
        $invoice->invoiceLine[1]->item->classifiedTaxCategory->id =
            ElectronicAddressHelper::fetchFromValue('S', Id::class);
        $invoice->invoiceLine[1]->item->classifiedTaxCategory->taxScheme = new TaxScheme();
        $invoice->invoiceLine[1]->item->classifiedTaxCategory->taxScheme->id =
            ElectronicAddressHelper::fetchFromValue('VAT', Id::class);
        $invoice->invoiceLine[1]->item->classifiedTaxCategory->percent = 6;

        $invoice->invoiceLine[2]->lineExtensionAmount = AmountHelper::fetchFromValue(10.00, LineExtensionAmount::class);
        $invoice->invoiceLine[2]->item = new Item();
        $invoice->invoiceLine[2]->item->classifiedTaxCategory = new ClassifiedTaxCategory();
        $invoice->invoiceLine[2]->item->classifiedTaxCategory->id =
            ElectronicAddressHelper::fetchFromValue('S', Id::class);
        $invoice->invoiceLine[2]->item->classifiedTaxCategory->taxScheme = new TaxScheme();
        $invoice->invoiceLine[2]->item->classifiedTaxCategory->taxScheme->id =
            ElectronicAddressHelper::fetchFromValue('VAT', Id::class);
        $invoice->invoiceLine[2]->item->classifiedTaxCategory->percent = 21;


        $event = new PreSerializeEvent(
            SerializationContext::create()->setVersion(Invoice::VERSION_2_0),
            $invoice,
            ['name' => Invoice::class]
        );

        $subscriber = new TaxCategoryEventSubscriber();
        $subscriber->setTaxTotal($event);

        $this->assertInstanceOf(TaxTotal::class, $invoice->taxTotal);
        $this->assertEquals(11.91, $invoice->taxTotal->taxAmount->amount);
        $this->assertCount(2, $invoice->taxTotal->taxSubtotal);
        $this->assertEquals(6.00, $invoice->taxTotal->taxSubtotal[0]->taxableAmount->amount);
        $this->assertEquals(55.00, $invoice->taxTotal->taxSubtotal[1]->taxableAmount->amount);
    }
}
