<?php

namespace DMT\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\Type\LineExtensionAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\PayableAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\PayableRoundingAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\TaxExclusiveAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\TaxInclusiveAmount;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

class LegalMonetaryTotalEventSubscriber implements EventSubscriberInterface
{

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): iterable
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'interface' => Invoice::class,
                'method' => 'setLegalMonataryTotals',
                'format' => 'xml',
            ]
        ];
    }

    public function setLegalMonataryTotals(PreSerializeEvent $event)
    {
        /** @var Invoice $invoice */
        $invoice = $event->getObject();

        $lineExtensionAmount = 0.0;
        foreach ($invoice->invoiceLine as $invoiceLine) {
            $lineExtensionAmount += round($invoiceLine->lineExtensionAmount->amount, 2);
        }

        $charges = 0.0;
        $discounts = 0.0;
        foreach ($invoice->allowanceCharge as $allowanceCharge) {
            if ($allowanceCharge->chargeIndicator) {
                $charges += round($allowanceCharge->amount->amount, 2);
            } else {
                $discounts += round($allowanceCharge->amount->amount, 2);
            }
        }
        $taxExclusiveAmount = $lineExtensionAmount + $charges - $discounts;
        $taxInclusiveAmount = $taxExclusiveAmount + $invoice->taxTotal->taxAmount->amount;

        $invoice->legalMonetaryTotal ??= new Invoice\LegalMonetaryTotal();
        $invoice->legalMonetaryTotal->lineExtensionAmount ??= new LineExtensionAmount();
        $invoice->legalMonetaryTotal->lineExtensionAmount->amount = $lineExtensionAmount;
        $invoice->legalMonetaryTotal->taxExclusiveAmount ??= new TaxExclusiveAmount();
        $invoice->legalMonetaryTotal->taxExclusiveAmount->amount = $taxExclusiveAmount;
        $invoice->legalMonetaryTotal->taxInclusiveAmount ??= new TaxInclusiveAmount();
        $invoice->legalMonetaryTotal->taxInclusiveAmount->amount = $taxInclusiveAmount;
        $invoice->legalMonetaryTotal->payableAmount ??= new PayableAmount();
        $invoice->legalMonetaryTotal->payableAmount->amount ??= $taxInclusiveAmount;
        if ($invoice->legalMonetaryTotal->payableAmount->amount != $taxInclusiveAmount) {
            $invoice->legalMonetaryTotal->payableRoundingAmount ??= new PayableRoundingAmount();
            $invoice->legalMonetaryTotal->payableRoundingAmount->amount =
                round($invoice->legalMonetaryTotal->payableAmount->amount - $taxInclusiveAmount, 2);
        }
    }
}
