<?php

namespace DMT\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice\Type\AmountType;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

readonly class CurrencyEventSubscriber implements EventSubscriberInterface
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
                'interface' => AmountType::class,
                'method' => 'addDefaultCurrency',
                'format' => 'xml'
            ]
        ];
    }

    public function addDefaultCurrency(PreSerializeEvent $event): void
    {
        /** @var AmountType $amount */
        $amount = $event->getObject();

        if ($amount == '') {
            return;
        }

        $amount->setCurrency($amount->currencyId ?? $this->defaultCurrency);
    }
}