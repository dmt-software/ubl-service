<?php

namespace DMT\Ubl\Service;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Event\AmountCurrencyEventSubscriber;
use DMT\Ubl\Service\Event\ElectronicAddressSchemeEventSubscriber;
use DMT\Ubl\Service\Event\InvoiceCustomizationEventSubscriber;
use DMT\Ubl\Service\Event\NormalizeAddressEventSubscriber;
use DMT\Ubl\Service\Event\QuantityUnitEventSubscriber;
use DMT\Ubl\Service\Event\SkipWhenEmptyEventSubscriber;
use DMT\Ubl\Service\Handler\UnionHandler;
use DMT\Ubl\Service\List\ElectronicAddressScheme;
use DMT\Ubl\Service\Transformer\ObjectToEntityTransformer;
use DMT\Ubl\Service\Transformer\EntityToObjectTransformer;
use InvalidArgumentException;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class InvoiceService
{
    /**
     * Check if the identifier is valid (based on its format only).
     *
     * @param string $identifier The identifier to check, e.g. vat number.
     * @param string|ElectronicAddressScheme $type The type of the identifier.
     * @return string The identifier (formatted) as it should be used within the UBL documents.
     * @throws InvalidArgumentException When the given identifier is invalid and can not be sanitized.
     */
    public function checkIdentifier(string $identifier, string|ElectronicAddressScheme $type): string
    {
        if (is_string($type)) {
            $type = ElectronicAddressScheme::lookup($type);
        }

        return $type->getFormatter()->format($identifier);
    }

    /**
     * Transform an UBL invoice entity into a custom invoice object.
     *
     * @param Invoice $invoice An UBL-Invoice object
     * @param EntityToObjectTransformer $transformer The transformer to use
     * @return object
     */
    public function fromInvoice(Invoice $invoice, EntityToObjectTransformer $transformer): object
    {
        return $transformer->transform($invoice);
    }

    /**
     * Get an object representation of an UBL-Invoice xml message.
     *
     * @param string $xml An incoming UBL-Invoice message to deserialize
     * @return Invoice
     */
    public function fromXml(string $xml): Invoice
    {
        return $this->getSerializer()->deserialize($xml, Invoice::class, 'xml');
    }

    /**
     * Transform an invoice object into a UBL Invoice.
     *
     * @param object $object Custom representation of an invoice
     * @param ObjectToEntityTransformer $transformer The transformer to use
     * @return Invoice
     */
    public function toInvoice(object $object, ObjectToEntityTransformer $transformer): Invoice
    {
        return $transformer->transform($object);
    }

    /**
     * Get an UBL-Invoice xml message for an Invoice.
     *
     * @param Invoice $invoice
     * @param string $version
     *
     * @return string
     */
    public function toXml(Invoice $invoice, string $version = Invoice::DEFAULT_VERSION): string
    {
        $context = SerializationContext::create()->setVersion($version);

        return $this->getSerializer()->serialize($invoice, 'xml', $context);
    }

    private function getSerializer(): Serializer
    {
        return SerializerBuilder::create()
            ->enableEnumSupport()
            ->configureListeners(function (EventDispatcher $dispatcher) {
                $dispatcher->addSubscriber(new SkipWhenEmptyEventSubscriber());
                $dispatcher->addSubscriber(new AmountCurrencyEventSubscriber());
                $dispatcher->addSubscriber(new ElectronicAddressSchemeEventSubscriber());
                $dispatcher->addSubscriber(new InvoiceCustomizationEventSubscriber());
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
