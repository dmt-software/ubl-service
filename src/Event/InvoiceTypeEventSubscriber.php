<?php

namespace DMT\Ubl\Service\Event;

use BackedEnum;
use DMT\Ubl\Service\Entity\Invoice\Type\InvoiceTypeCode;
use DMT\Ubl\Service\List\InvoiceType;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

readonly class InvoiceTypeEventSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): iterable
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'class' => InvoiceTypeCode::class,
                'method' => 'applyInvoiceTypeCode',
                'format' => 'xml'
            ]
        ];
    }

    public function applyInvoiceTypeCode(PreSerializeEvent $event): void
    {
        /** @var InvoiceTypeCode $object */
        $object = $event->getObject();

        if (!$object->code instanceof BackedEnum) {
            $object->code = InvoiceType::tryFrom($object->code) ?? InvoiceType::Normal;
        }
    }
}