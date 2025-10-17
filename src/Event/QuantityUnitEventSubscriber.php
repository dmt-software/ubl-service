<?php

namespace DMT\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice\Type\QuantityType;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Metadata\PropertyMetadata;

final readonly class QuantityUnitEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private string $defaultUnit = QuantityType::DEFAULT_UNIT_CODE)
    {
    }

    /**
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents(): iterable
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'method' => 'prepareXmlValue',
                'format' => 'xml'
            ],
            [
                'event' => 'serializer.pre_serialize',
                'interface' => QuantityType::class,
                'method' => 'addDefaultUnit',
                'format' => 'xml'
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
            fn (PropertyMetadata $metadata) => is_a($metadata->type['name'], QuantityType::class, true)
        );

        foreach ($properties as $propertyMetadata) {
            $prop = $propertyMetadata->name;
            $type = $propertyMetadata->type['name'];

            if (is_a($type, QuantityType::class, true) && is_int($object->$prop)) {
                $quantity = new $type;
                $quantity->quantity = $object->$prop;
                $object->$prop = $quantity;
            }
        }
    }

    public function addDefaultUnit(PreSerializeEvent $event): void
    {
        /** @var QuantityType $unit */
        $unit = $event->getObject();

        if ($unit == '') {
            return;
        }

        $unit->setUnit($unit->unitCode ?? $this->defaultUnit);
    }
}