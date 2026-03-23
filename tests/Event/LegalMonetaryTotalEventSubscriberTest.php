<?php

namespace DMT\Test\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\CommonAggregateComponents\AllowanceCharge;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\InvoiceLine;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\LegalMonetaryTotal;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\TaxTotal;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Amount;
use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Event\LegalMonetaryTotalEventSubscriber;
use DMT\Ubl\Service\Helper\Invoice\AmountHelper;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\SerializationContext;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LegalMonetaryTotalEventSubscriberTest extends TestCase
{
    #[DataProvider(methodName: 'provideInvoice')]
    public function testSetLegalMonetaryTotal(Invoice $invoice, string $amountEntry, float $amount): void
    {
        $event = new PreSerializeEvent(
            SerializationContext::create(),
            $invoice,
            ['name' => Invoice::class]
        );

        $subscriber = new LegalMonetaryTotalEventSubscriber();
        $subscriber->setLegalMonetaryTotal($event);

        $this->assertEquals($amount, $invoice->legalMonetaryTotal->{$amountEntry}->amount);
    }

    public static function provideInvoice(): iterable
    {
        $invoice = new Invoice();
        $invoice->invoiceLine = [0 => new InvoiceLine()];
        $invoice->invoiceLine[0]->lineExtensionAmount = AmountHelper::fetchFromValue(100.00, Amount::class);
        $invoice->legalMonetaryTotal = new LegalMonetaryTotal();
        $invoice->legalMonetaryTotal->payableAmount = AmountHelper::fetchFromValue(100.00, Amount::class);

        yield 'invoice sums up lines' => [$invoice, 'lineExtensionAmount', 100.00];
        yield 'invoice tax exclusive' => [$invoice, 'taxExclusiveAmount', 100.00];

        $invoice1 = clone($invoice);
        $invoice1->taxTotal = new TaxTotal();
        $invoice1->taxTotal->taxAmount = AmountHelper::fetchFromValue(21.00, Amount::class);
        $invoice1->legalMonetaryTotal = new LegalMonetaryTotal();
        $invoice1->legalMonetaryTotal->payableAmount = AmountHelper::fetchFromValue(121.00, Amount::class);

        yield 'invoice with tax, tax exclusive' => [$invoice1, 'taxExclusiveAmount', 100.00];
        yield 'invoice with tax, tax inclusive' => [$invoice1, 'taxInclusiveAmount', 121.00];

        $invoice2 = clone($invoice);
        $invoice2->taxTotal = new TaxTotal();
        $invoice2->taxTotal->taxAmount = AmountHelper::fetchFromValue(21.00, Amount::class);
        $invoice2->legalMonetaryTotal = new LegalMonetaryTotal();
        $invoice2->legalMonetaryTotal->payableAmount = AmountHelper::fetchFromValue(120.99, Amount::class);

        yield 'invoice with correction, tax inclusive' => [$invoice2, 'taxInclusiveAmount', 121.00];
        yield 'invoice with corrected rounding error' => [$invoice2, 'payableRoundingAmount', -0.01];

        $invoice3 = clone($invoice);
        $invoice3->taxTotal = new TaxTotal();
        $invoice3->taxTotal->taxAmount = AmountHelper::fetchFromValue(23.10, Amount::class);
        $invoice3->allowanceCharge = [0 => new AllowanceCharge()];
        $invoice3->allowanceCharge[0]->chargeIndicator = true;
        $invoice3->allowanceCharge[0]->amount = AmountHelper::fetchFromValue(10.00, Amount::class);
        $invoice3->legalMonetaryTotal = new LegalMonetaryTotal();
        $invoice3->legalMonetaryTotal->payableAmount = AmountHelper::fetchFromValue(123.10, Amount::class);

        yield 'invoice with charges, change amount' => [$invoice3, 'chargeTotalAmount', 10.00];
        yield 'invoice with charges, tax exclusive' => [$invoice3, 'taxExclusiveAmount', 110.00];

        $invoice4 = clone($invoice3);
        $invoice4->taxTotal = new TaxTotal();
        $invoice4->taxTotal->taxAmount = AmountHelper::fetchFromValue(18.90, Amount::class);
        $invoice4->allowanceCharge[0] = clone($invoice3->allowanceCharge[0]);
        $invoice4->allowanceCharge[0]->chargeIndicator = false;
        $invoice4->legalMonetaryTotal = new LegalMonetaryTotal();
        $invoice4->legalMonetaryTotal->payableAmount = AmountHelper::fetchFromValue(108.91, Amount::class);

        yield 'invoice with allowances, allowance amount' => [$invoice4, 'allowanceTotalAmount', 10.00];
        yield 'invoice with allowances, tax exclusive' => [$invoice4, 'taxExclusiveAmount', 90.00];
        yield 'invoice with allowances, correction' => [$invoice4, 'payableRoundingAmount', 0.01];
    }
}
