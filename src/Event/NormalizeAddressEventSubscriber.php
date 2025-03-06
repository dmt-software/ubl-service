<?php

namespace DMT\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice\Address;
use DMT\Ubl\Service\Entity\Invoice\PostalAddress;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

readonly class NormalizeAddressEventSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): iterable
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'class' => Address::class,
                'method' => 'normalizeAddress',
                'format' => 'xml'
            ],
            [
                'event' => 'serializer.pre_serialize',
                'class' => PostalAddress::class,
                'method' => 'normalizeAddress',
                'format' => 'xml'
            ],
        ];
    }

    public function normalizeAddress(PreSerializeEvent $event): void
    {
        if (!$event->getContext()->hasAttribute('version')) {
            return;
        }

        /** @var Address|PostalAddress $address */
        $address = $event->getObject();

        if (version_compare($event->getContext()->getAttribute('version'), "2.0", '>=')) {
            $address->streetName .= ' ' . $address->buildingNumber;
        }
    }
}