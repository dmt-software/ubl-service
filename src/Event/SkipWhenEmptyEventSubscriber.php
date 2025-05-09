<?php

namespace DMT\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice\Type\AmountType;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * This events clears xml value objects that have default attribute values, but without a text value present.
 */
class SkipWhenEmptyEventSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): iterable
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'method' => 'emptyElements',
                'format' => 'xml',
            ]
        ];
    }

    public function emptyElements(PreSerializeEvent $event): void
    {
        $object = $event->getObject();
        $metadata = $event->getContext()
            ->getMetadataFactory()
            ->getMetadataForClass(get_class($object));

        $properties = array_filter(
            $metadata->propertyMetadata,
            fn (PropertyMetadata $metadata) => $metadata->skipWhenEmpty
        );

        foreach ($properties as $property => $metadata) {
            $value = $object->{$property};

            if (!empty($value)) {
                continue;
            }

            if ($object instanceof AmountType) {
                $object->currencyId = null;
            }

            $object->{$property} = null;
        }
    }
}