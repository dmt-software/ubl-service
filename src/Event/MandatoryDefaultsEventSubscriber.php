<?php

namespace DMT\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\CommonAggregateComponents\CreditNoteLine;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\InvoiceLine;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\OrderReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Party;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PartyLegalEntity;
use DMT\Ubl\Service\Entity\CreditNote;
use DMT\Ubl\Service\Entity\Document;
use DMT\Ubl\Service\Entity\Invoice;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

final readonly class MandatoryDefaultsEventSubscriber implements EventSubscriberInterface
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
                'method' => 'setDefaultOrderReference',
                'format' => 'xml',
            ],
            [
                'event' => 'serializer.pre_serialize',
                'interface' => Document::class,
                'method' => 'setDefaultLineNumbers',
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
        /** @var Invoice|CreditNote $document */
        $document = $event->getObject();

        if (version_compare($event->getContext()->getAttribute('version'), "2.0", '>=')) {
            if (empty($document?->orderReference->id)) {
                $document->orderReference ??= new OrderReference();
                $document->orderReference->id = 'NA';
            }
        }
    }

    public function setDefaultLineNumbers(PreSerializeEvent $event): void
    {
        /** @var Invoice|CreditNote $document */
        $document = $event->getObject();

        $lines = $document->invoiceLine ?? $document->creditNoteLine ?? [];

        $max = max(array_map(fn(InvoiceLine|CreditNoteLine $line) => intval($line->id), $lines)) + 1;
        foreach ($lines as $key => $line) {
            if (!$line->id) {
                $line->id = $key + $max;
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
