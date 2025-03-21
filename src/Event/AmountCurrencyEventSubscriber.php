<?php

namespace DMT\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice\Type\AmountType;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\Metadata\PropertyMetadata;

readonly class AmountCurrencyEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private string $defaultCurrency = 'EUR')
    {
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): iterable
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'method' => 'prepareXmlValue',
                'format' => 'xml',
            ],
            [
                'event' => 'serializer.pre_serialize',
                'interface' => AmountType::class,
                'method' => 'addDefaultCurrency',
                'format' => 'xml',
            ]
        ];
    }

    public function prepareXmlValue(PreSerializeEvent $event): void
    {
        $object = $event->getObject();

        $classMetadata = $event->getContext()
            ->getMetadataFactory()
            ->getMetadataForClass(get_class($object));

        $properties = array_filter(
            $classMetadata->propertyMetadata,
            fn (PropertyMetadata $metadata) => is_a($metadata->type['name'], AmountType::class, true)
        );

        foreach ($properties as $propertyMetadata) {
            $prop = $propertyMetadata->name;
            $type = $propertyMetadata->type['name'];

            if (is_a($type, AmountType::class, true) && is_float($object->$prop)) {
                $amount = new $type;
                $amount->amount = $object->$prop;
                $object->$prop = $amount;
            }
        }
    }

    public function addDefaultCurrency(PreSerializeEvent $event): void
    {
        /** @var AmountType $amount */
        $amount = $event->getObject();

        if (empty($amount->amount)) {
            return;
        }

        $amount->setCurrency($amount->currencyId ?? $this->defaultCurrency);
    }
}