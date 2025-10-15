<?php

namespace DMT\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\OrderReference;
use DMT\Ubl\Service\Entity\Invoice\Party;
use DMT\Ubl\Service\Entity\Invoice\PartyLegalEntity;
use DMT\Ubl\Service\Entity\InvoiceLine;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

final readonly class MandatoryDefaultsEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): iterable
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'interface' => Invoice::class,
                'method' => 'setDefaultOrderReference',
                'format' => 'xml',
            ],
            [
                'event' => 'serializer.pre_serialize',
                'interface' => Invoice::class,
                'method' => 'setDefaultInvoiceLineNumbers',
                'format' => 'xml',
            ],
            [
                'event' => 'serializer.pre_serialize',
                'interface' => Party::class,
                'method' => 'setPartyDefaultLegalName',
                'format' => 'xml',
            ]
        ];
    }

    public function setDefaultOrderReference(PreSerializeEvent $event): void
    {
        /** @var Invoice $invoice */
        $invoice = $event->getObject();

        if (version_compare($event->getContext()->getAttribute('version'), "2.0", '>=')) {
            if (empty($invoice?->orderReference->id)) {
                $invoice->orderReference ??= new OrderReference();
                $invoice->orderReference->id = 'NA';
            }
        }
    }

    public function setDefaultInvoiceLineNumbers(PreSerializeEvent $event): void
    {
        /** @var Invoice $invoice */
        $invoice = $event->getObject();

        $max = max(array_map(fn(InvoiceLine $line) => intval($line->id), $invoice->invoiceLine ?? [])) + 1;
        foreach ($invoice->invoiceLine ?? [] as $key => $invoiceLine) {
            if (!$invoiceLine->id) {
                $invoiceLine->id = $key + $max;
            }
        }
    }

    public function setPartyDefaultLegalName(PreSerializeEvent $event): void
    {
        /** @var Party $party*/
        $party = $event->getObject();

        if (version_compare($event->getContext()->getAttribute('version'), "1.2", '>=')) {
            if (empty($party?->partyLegalEntity->registrationName)) {
                $party->partyLegalEntity ??= new PartyLegalEntity();
                $party->partyLegalEntity->registrationName = $party->partyName?->name;
            }
        }
    }
}