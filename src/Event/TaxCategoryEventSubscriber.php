<?php

namespace DMT\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\CommonAggregateComponents\ClassifiedTaxCategory;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\CreditNoteLine;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\InvoiceLine;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PostalAddress;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\TaxCategory;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\TaxScheme;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\TaxSubtotal;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\TaxTotal;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Id;
use DMT\Ubl\Service\Entity\CommonBasicComponents\TaxableAmount;
use DMT\Ubl\Service\Entity\CommonBasicComponents\TaxAmount;
use DMT\Ubl\Service\Entity\CreditNote;
use DMT\Ubl\Service\Entity\Document;
use DMT\Ubl\Service\Entity\Invoice;
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
                'interface' => Document::class,
                'method' => 'setClassifiedTaxCategories',
                'format' => 'xml',
            ],
            [
                'event' => 'serializer.pre_serialize',
                'interface' => Document::class,
                'method' => 'setTaxTotal',
                'format' => 'xml',
            ],
        ];
    }

    public function setClassifiedTaxCategories(PreSerializeEvent $event): void
    {
        /** @var Invoice|CreditNote $document */
        $document = $event->getObject();

        $forExport = $this->isDocumentForExport(
            $document->accountingSupplierParty?->party?->postalAddress,
            $document->accountingCustomerParty?->party?->postalAddress
        );

        foreach ($document->invoiceLine ?? $document->creditNoteLine ?? [] as $line) {
            $this->setClassifiedTaxCategory($line, $forExport);
        }
    }

    public function setTaxTotal(PreSerializeEvent $event): void
    {
        /** @var Invoice|CreditNote $document */
        $document = $event->getObject();
        $lines = $document->invoiceLine ?? $document->creditNoteLine ?? [];

        usort(
            $lines,
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
        foreach ($lines as $line) {
            if ($taxCategory->id->id != $line->item->classifiedTaxCategory->id->id
                || $taxCategory->percent != $line->item->classifiedTaxCategory->percent
            ) {
                $taxCategory = new TaxCategory();
                $taxCategory->id = clone($line->item->classifiedTaxCategory->id);
                $taxCategory->percent = $line->item->classifiedTaxCategory->percent;
                $taxCategory->taxScheme = clone($line->item->classifiedTaxCategory->taxScheme);

                $taxSubtotal = new TaxSubtotal();
                $taxSubtotal->taxCategory = $taxCategory;
                $taxSubtotal->taxableAmount = new TaxableAmount();
                $taxSubtotal->taxableAmount->amount = 0.0;

                $taxTotal->taxSubtotal[] = $taxSubtotal;
            }

            if (!isset($taxSubtotal)) {
                continue;
            }

            $taxSubtotal->taxableAmount->amount += round($line->lineExtensionAmount->amount, 2);
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
            foreach ($lines as $line) {
                $taxTotal->taxAmount->amount += round($line->taxTotal->taxAmount->amount, 2);
            }
        }

        $document->taxTotal = $taxTotal;
    }

    private function setClassifiedTaxCategory(InvoiceLine|CreditNoteLine $line, bool $forExport): void
    {
        $percentage = $line->item?->classifiedTaxCategory?->percent;
        if (!$percentage && $line->taxTotal?->taxAmount && $line->lineExtensionAmount->amount > 0) {
            $percentage = round(($line->taxTotal->taxAmount->amount / $line->lineExtensionAmount->amount) * 100);
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

        $line->taxTotal ??= new TaxTotal();
        $line->taxTotal->taxAmount ??= new TaxAmount();
        $line->taxTotal->taxAmount->amount ??= round($line->lineExtensionAmount->amount * $percentage / 100, 2);

        $line->item->classifiedTaxCategory = $classifiedTaxCategory;
    }

    private function isDocumentForExport(null|PostalAddress $supplier, null|PostalAddress $customer): bool
    {
        if ($supplier === null || $customer === null) {
            return false;
        }

        return $supplier->country?->identificationCode?->code != $customer->country?->identificationCode?->code;
    }
}
