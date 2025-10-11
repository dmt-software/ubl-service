<?php

namespace DMT\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\OrderReference;
use DMT\Ubl\Service\Entity\Invoice\Party;
use DMT\Ubl\Service\Entity\Invoice\PartyLegalEntity;
use DMT\Ubl\Service\Entity\Invoice\StandardItemIdentification;
use DMT\Ubl\Service\List\ElectronicAddressScheme;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

class MandatoryDefaultsEventSubscriber implements EventSubscriberInterface
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
                'method' => 'setInvoiceDefaults',
                'format' => 'xml',
            ],
            [
                'event' => 'serializer.pre_serialize',
                'interface' => Party::class,
                'method' => 'setPartyDefaults',
                'format' => 'xml',
            ],
            [
                'event' => 'serializer.pre_serialize',
                'interface' => StandardItemIdentification::class,
                'method' => 'setStandardItemIdentificationDefaults',
                'format' => 'xml',
            ],
        ];
    }

    public function setInvoiceDefaults(PreSerializeEvent $event): void
    {
        /** @var Invoice $invoice */
        $invoice = $event->getObject();

        if (version_compare($event->getContext()->getAttribute('version'), "2.0", '>=')) {
            if (!$invoice?->orderReference->id) {
                $invoice->orderReference ??= new OrderReference();
                $invoice->orderReference->id = 'NA';
            }
        }
    }

    public function setPartyDefaults(PreSerializeEvent $event): void
    {
        /** @var Party $party*/
        $party = $event->getObject();

        if (version_compare($event->getContext()->getAttribute('version'), "1.2", '>=')) {
            if (!$party->partyLegalEntity?->registrationName) {
                $party->partyLegalEntity ??= new PartyLegalEntity();
                $party->partyLegalEntity->registrationName = $party->partyName?->name;
            }
        }
    }

    public function setStandardItemIdentificationDefaults(PreSerializeEvent $event): void
    {
        /** @var StandardItemIdentification $identification */
        $identification = $event->getObject();

        if (version_compare($event->getContext()->getAttribute('version'), "1.1", '>=')) {
            if ($identification->id?->id) {
                $identification->id->schemeId = ElectronicAddressScheme::GTINNumber;
            }
        }
    }
}