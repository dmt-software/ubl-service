<?php

namespace DMT\Ubl\Service;

use DMT\Ubl\Service\Event\AmountCurrencyEventSubscriber;
use DMT\Ubl\Service\Event\ElectronicAddressSchemeEventSubscriber;
use DMT\Ubl\Service\Event\InvoiceCustomizationEventSubscriber;
use DMT\Ubl\Service\Event\NormalizeAddressEventSubscriber;
use DMT\Ubl\Service\Event\QuantityUnitEventSubscriber;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class InvoiceService
{
    public function getSerializer(): Serializer
    {
        return SerializerBuilder::create()
            ->enableEnumSupport()
            ->configureListeners(function (EventDispatcher $dispatcher) {
                $dispatcher->addSubscriber(new AmountCurrencyEventSubscriber());
                $dispatcher->addSubscriber(new ElectronicAddressSchemeEventSubscriber());
                $dispatcher->addSubscriber(new InvoiceCustomizationEventSubscriber());
                $dispatcher->addSubscriber(new NormalizeAddressEventSubscriber());
                $dispatcher->addSubscriber(new QuantityUnitEventSubscriber());
            })
            ->build();
    }
}
