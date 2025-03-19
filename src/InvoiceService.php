<?php

namespace DMT\Ubl\Service;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Event\AmountCurrencyEventSubscriber;
use DMT\Ubl\Service\Event\ElectronicAddressSchemeEventSubscriber;
use DMT\Ubl\Service\Event\InvoiceCustomizationEventSubscriber;
use DMT\Ubl\Service\Event\InvoiceTypeEventSubscriber;
use DMT\Ubl\Service\Event\NormalizeAddressEventSubscriber;
use DMT\Ubl\Service\Event\QuantityUnitEventSubscriber;
use DMT\Ubl\Service\Handler\UnionHandler;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class InvoiceService
{
    public function __construct(private ?Serializer $serializer = null)
    {
        $this->serializer = $this->serializer ?? $this->getSerializer();
    }

    public function fromXml(string $xml): Invoice
    {
        return $this->serializer->deserialize($xml, Invoice::class, 'xml');
    }

    public function toXml(Invoice $invoice, string $version = Invoice::DEFAULT_VERSION): string
    {
        $context = SerializationContext::create()->setVersion($version);

        return $this->serializer->serialize($invoice, 'xml', $context);
    }

    private function getSerializer(): Serializer
    {
        return SerializerBuilder::create()
            ->enableEnumSupport()
            ->configureListeners(function (EventDispatcher $dispatcher) {
                $dispatcher->addSubscriber(new AmountCurrencyEventSubscriber());
                $dispatcher->addSubscriber(new ElectronicAddressSchemeEventSubscriber());
                $dispatcher->addSubscriber(new InvoiceCustomizationEventSubscriber());
                $dispatcher->addSubscriber(new InvoiceTypeEventSubscriber());
                $dispatcher->addSubscriber(new NormalizeAddressEventSubscriber());
                $dispatcher->addSubscriber(new QuantityUnitEventSubscriber());
            })
            ->addDefaultHandlers()
            ->configureHandlers(function (HandlerRegistry $registry) {
                $registry->registerSubscribingHandler(new UnionHandler());
            })
            ->build();
    }
}
