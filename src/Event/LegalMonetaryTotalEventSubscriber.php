<?php

namespace DMT\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\LegalMonetaryTotal;
use DMT\Ubl\Service\Entity\Invoice\Type\AllowanceTotalAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\ChargeTotalAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\LineExtensionAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\PayableAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\PayableRoundingAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\TaxExclusiveAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\TaxInclusiveAmount;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

final readonly class LegalMonetaryTotalEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): iterable
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'class' => Invoice::class,
                'method' => 'setLegalMonetaryTotal',
                'format' => 'xml',
            ]
        ];
    }

    public function setLegalMonetaryTotal(PreSerializeEvent $event)
    {
        /** @var Invoice $invoice */
        $invoice = $event->getObject();

        $lineExtensionAmount = 0.0;
        foreach ($invoice->invoiceLine ?? [] as $invoiceLine) {
            $lineExtensionAmount += round($invoiceLine->lineExtensionAmount->amount, 2);
        }

        $chargeAmount = 0.0;
        $allowanceAmount = 0.0;
        foreach ($invoice->allowanceCharge ?? [] as $allowanceCharge) {
            if ($allowanceCharge->chargeIndicator) {
                $chargeAmount += round($allowanceCharge->amount->amount, 2);
            } else {
                $allowanceAmount += round($allowanceCharge->amount->amount, 2);
            }
        }

        $taxExclusiveAmount = $lineExtensionAmount + $chargeAmount - $allowanceAmount;
        $taxInclusiveAmount = $taxExclusiveAmount + $invoice->taxTotal?->taxAmount?->amount ?? 0.0;
        $prepaidAmount = $invoice->legalMonetaryTotal?->prepaidAmount?->amount ?? 0.0;

        $invoice->legalMonetaryTotal ??= new LegalMonetaryTotal();
        $invoice->legalMonetaryTotal->lineExtensionAmount ??= new LineExtensionAmount();
        $invoice->legalMonetaryTotal->lineExtensionAmount->amount = $lineExtensionAmount;
        $invoice->legalMonetaryTotal->taxExclusiveAmount ??= new TaxExclusiveAmount();
        $invoice->legalMonetaryTotal->taxExclusiveAmount->amount = $taxExclusiveAmount;
        $invoice->legalMonetaryTotal->taxInclusiveAmount ??= new TaxInclusiveAmount();
        $invoice->legalMonetaryTotal->taxInclusiveAmount->amount = $taxInclusiveAmount;
        $invoice->legalMonetaryTotal->payableAmount ??= new PayableAmount();
        $invoice->legalMonetaryTotal->payableAmount->amount ??= $taxInclusiveAmount - $prepaidAmount;

        if ($allowanceAmount) {
            $invoice->legalMonetaryTotal->allowanceTotalAmount ??= new AllowanceTotalAmount();
            $invoice->legalMonetaryTotal->allowanceTotalAmount->amount = $allowanceAmount;
        }
        if ($chargeAmount) {
            $invoice->legalMonetaryTotal->chargeTotalAmount ??= new ChargeTotalAmount();
            $invoice->legalMonetaryTotal->chargeTotalAmount->amount = $chargeAmount;
        }
        if ($invoice->legalMonetaryTotal->payableAmount->amount != $taxInclusiveAmount - $prepaidAmount) {
            $invoice->legalMonetaryTotal->payableRoundingAmount ??= new PayableRoundingAmount();
            $invoice->legalMonetaryTotal->payableRoundingAmount->amount =
                round($invoice->legalMonetaryTotal->payableAmount->amount - $taxInclusiveAmount - $prepaidAmount, 2);
        }
    }
}
