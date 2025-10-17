<?php

namespace DMT\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\ClassifiedTaxCategory;
use DMT\Ubl\Service\Entity\Invoice\PostalAddress;
use DMT\Ubl\Service\Entity\Invoice\TaxCategory;
use DMT\Ubl\Service\Entity\Invoice\TaxScheme;
use DMT\Ubl\Service\Entity\Invoice\TaxSubtotal;
use DMT\Ubl\Service\Entity\Invoice\TaxTotal;
use DMT\Ubl\Service\Entity\Invoice\Type\Id;
use DMT\Ubl\Service\Entity\Invoice\Type\TaxableAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\TaxAmount;
use DMT\Ubl\Service\Entity\InvoiceLine;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

final readonly class TaxCategoryEventSubscriber implements EventSubscriberInterface
{
    /**
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents(): iterable
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'interface' => Invoice::class,
                'method' => 'setClassifiedTaxCategories',
                'format' => 'xml',
            ],
            [
                'event' => 'serializer.pre_serialize',
                'interface' => Invoice::class,
                'method' => 'setTaxTotal',
                'format' => 'xml',
            ]
        ];
    }

    public function setClassifiedTaxCategories(PreSerializeEvent $event): void
    {
        /** @var Invoice $invoice */
        $invoice = $event->getObject();

        $forExport = $this->isInvoiceForExport(
            $invoice->accountingSupplierParty?->party?->postalAddress,
            $invoice->accountingCustomerParty?->party?->postalAddress
        );

        foreach ($invoice->invoiceLine as $invoiceLine) {
            $this->setClassifiedTaxCategory($invoiceLine, $forExport);
        }
    }

    public function setTaxTotal(PreSerializeEvent $event): void
    {
        /** @var Invoice $invoice */
        $invoice = $event->getObject();
        $invoiceLines = $invoice->invoiceLine ?? [];

        usort(
            $invoiceLines,
            function (InvoiceLine $a, InvoiceLine $b) {
                $at = $a->item->classifiedTaxCategory;
                $bt = $b->item->classifiedTaxCategory;

                if ($at->taxScheme->id->id == $bt->taxScheme->id->id) {
                    return $at->percent <=> $bt->percent;
                }

                return $at->taxScheme->id->id <=> $bt->taxScheme->id->id;
            }
        );

        $taxCategory = null;
        $taxTotal = new TaxTotal();
        foreach ($invoiceLines as $invoiceLine) {
            if ($taxCategory->id->id != $invoiceLine->item->classifiedTaxCategory->id->id
                || $taxCategory->percent != $invoiceLine->item->classifiedTaxCategory->percent
            ) {
                $taxCategory = new TaxCategory();
                $taxCategory->id = clone($invoiceLine->item->classifiedTaxCategory->id);
                $taxCategory->percent = $invoiceLine->item->classifiedTaxCategory->percent;
                $taxCategory->taxScheme = clone($invoiceLine->item->classifiedTaxCategory->taxScheme);

                $taxSubtotal = new TaxSubtotal();
                $taxSubtotal->taxCategory = $taxCategory;
                $taxSubtotal->taxableAmount = new TaxableAmount();
                $taxSubtotal->taxableAmount->amount = 0.0;

                $taxTotal->taxSubtotal[] = $taxSubtotal;
            }

            if (!isset($taxSubtotal)) {
                continue;
            }

            $taxSubtotal->taxableAmount->amount += round($invoiceLine->lineExtensionAmount->amount, 2);
        }

        $taxTotal->taxAmount = new TaxAmount();
        $taxTotal->taxAmount->amount = 0.0;
        if (version_compare($event->getContext()->getAttribute('version'), "2.0", '>=')) {
            foreach ($taxTotal->taxSubtotal as $taxSubtotal) {
                $taxSubtotal->taxAmount = new TaxAmount();
                $taxSubtotal->taxAmount->amount = round(($taxSubtotal->taxableAmount->amount / 100) * $taxSubtotal->taxCategory->percent, 2);

                $taxTotal->taxAmount->amount += $taxSubtotal->taxAmount->amount;
            }
        } else {
            foreach ($invoiceLines as $invoiceLine) {
                $taxTotal->taxAmount->amount += round($invoiceLine->taxTotal->taxAmount->amount, 2);
            }
        }

        $invoice->taxTotal = $taxTotal;
    }

    private function setClassifiedTaxCategory(InvoiceLine $invoiceLine, bool $forExport): void
    {
        $percentage = $invoiceLine->item?->classifiedTaxCategory?->percent;
        if (!$percentage && $invoiceLine->taxTotal?->taxAmount && $invoiceLine->lineExtensionAmount->amount > 0) {
            $percentage = round(($invoiceLine->taxTotal->taxAmount->amount / $invoiceLine->lineExtensionAmount->amount) * 100);
        }

        if ($percentage === null) {
            return;
        }

        $taxSchemeId = TaxScheme::STANDARD_TAX_RATE;
        if (!$percentage) {
            $taxSchemeId = $forExport ? TaxScheme::EXPORT_TAX_FREE : TaxScheme::EXEMPT_FROM_TAX;
        }

        $classifiedTaxCategory ??= new ClassifiedTaxCategory();
        $classifiedTaxCategory->id ??= new Id();
        $classifiedTaxCategory->id->id ??= $taxSchemeId;
        $classifiedTaxCategory->percent = $percentage;
        $classifiedTaxCategory->taxScheme = new TaxScheme();
        $classifiedTaxCategory->taxScheme->id = new Id();
        $classifiedTaxCategory->taxScheme->id->id = 'VAT';

        $invoiceLine->taxTotal ??= new TaxTotal();
        $invoiceLine->taxTotal->taxAmount ??= new TaxAmount();
        $invoiceLine->taxTotal->taxAmount->amount ??= round($invoiceLine->lineExtensionAmount->amount * $percentage / 100, 2);

        $invoiceLine->item->classifiedTaxCategory = $classifiedTaxCategory;
    }

    private function isInvoiceForExport(null|PostalAddress $supplier, null|PostalAddress $customer): bool
    {
        if ($supplier === null || $customer === null) {
            return false;
        }

        return $supplier->country?->identificationCode?->code != $customer->country?->identificationCode?->code;
    }
}
