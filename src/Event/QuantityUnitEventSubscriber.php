<?php

namespace DMT\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice\Type\QuantityType;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

readonly class QuantityUnitEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private string $defaultUnit = QuantityType::DEFAULT_UNIT_CODE)
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
                'interface' => QuantityType::class,
                'method' => 'addDefaultUnit',
                'format' => 'xml'
            ]
        ];
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