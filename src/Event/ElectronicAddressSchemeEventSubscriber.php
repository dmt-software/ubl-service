<?php

namespace DMT\Ubl\Service\Event;

use BackedEnum;
use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\Type\ElectronicAddressType;
use DMT\Ubl\Service\List\ElectronicAddressScheme;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

class ElectronicAddressSchemeEventSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): iterable
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'interface' => ElectronicAddressType::class,
                'method' => 'addSchemeAttributes',
                'format' => 'xml'
            ]
        ];
    }

    public function addSchemeAttributes(PreSerializeEvent $event): void
    {
        /** @var ElectronicAddressType $object */
        $object = $event->getObject();

        if (!$object->schemeId instanceof BackedEnum) {
            $object->schemeId = ElectronicAddressScheme::tryFrom($object->schemeId);
        }

        if (!$object->schemeId) {
            return;
        }

        $version = Invoice::DEFAULT_VERSION;
        if ($event->getContext()->hasAttribute('version')) {
            $version = $event->getContext()->getAttribute('version');
        }

        $object->schemeAgencyId = $object->schemeId->getSchemeAgencyId($version);
        $object->schemeId = $object->schemeId->getSchemeId($version);
    }
}
