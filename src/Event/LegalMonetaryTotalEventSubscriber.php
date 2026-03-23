<?php

namespace DMT\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\CommonAggregateComponents\LegalMonetaryTotal;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Amount;
use DMT\Ubl\Service\Entity\CreditNote;
use DMT\Ubl\Service\Entity\Document;
use DMT\Ubl\Service\Entity\Invoice;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

final readonly class LegalMonetaryTotalEventSubscriber implements EventSubscriberInterface
{
    /**
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents(): iterable
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'class' => Document::class,
                'method' => 'setLegalMonetaryTotal',
                'format' => 'xml',
            ],
        ];
    }

    public function setLegalMonetaryTotal(PreSerializeEvent $event): void
    {
        /** @var Invoice|CreditNote $document */
        $document = $event->getObject();

        $lineExtensionAmount = 0.0;
        foreach ($document->invoiceLine ?? $document->creditNoteLine ?? [] as $line) {
            $lineExtensionAmount += round($line->lineExtensionAmount->amount, 2);
        }

        $chargeAmount = 0.0;
        $allowanceAmount = 0.0;
        foreach ($document->allowanceCharge ?? [] as $allowanceCharge) {
            if ($allowanceCharge->chargeIndicator) {
                $chargeAmount += round($allowanceCharge->amount->amount, 2);
            } else {
                $allowanceAmount += round($allowanceCharge->amount->amount, 2);
            }
        }

        $taxExclusiveAmount = $lineExtensionAmount + $chargeAmount - $allowanceAmount;
        $taxInclusiveAmount = $taxExclusiveAmount + $document->taxTotal?->taxAmount?->amount ?? 0.0;
        $prepaidAmount = $document->legalMonetaryTotal?->prepaidAmount?->amount ?? 0.0;

        $document->legalMonetaryTotal ??= new LegalMonetaryTotal();
        $document->legalMonetaryTotal->lineExtensionAmount ??= new Amount();
        $document->legalMonetaryTotal->lineExtensionAmount->amount = $lineExtensionAmount;
        $document->legalMonetaryTotal->taxExclusiveAmount ??= new Amount();
        $document->legalMonetaryTotal->taxExclusiveAmount->amount = $taxExclusiveAmount;
        $document->legalMonetaryTotal->taxInclusiveAmount ??= new Amount();
        $document->legalMonetaryTotal->taxInclusiveAmount->amount = $taxInclusiveAmount;
        $document->legalMonetaryTotal->payableAmount ??= new Amount();
        $document->legalMonetaryTotal->payableAmount->amount ??= $taxInclusiveAmount - $prepaidAmount;

        if ($allowanceAmount) {
            $document->legalMonetaryTotal->allowanceTotalAmount ??= new Amount();
            $document->legalMonetaryTotal->allowanceTotalAmount->amount = $allowanceAmount;
        }
        if ($chargeAmount) {
            $document->legalMonetaryTotal->chargeTotalAmount ??= new Amount();
            $document->legalMonetaryTotal->chargeTotalAmount->amount = $chargeAmount;
        }
        if ($document->legalMonetaryTotal->payableAmount->amount != $taxInclusiveAmount - $prepaidAmount) {
            $document->legalMonetaryTotal->payableRoundingAmount ??= new Amount();
            $document->legalMonetaryTotal->payableRoundingAmount->amount =
                round($document->legalMonetaryTotal->payableAmount->amount - $taxInclusiveAmount - $prepaidAmount, 2);
        }
    }
}
