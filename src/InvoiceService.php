<?php

namespace DMT\Ubl\Service;

use DMT\Ubl\Service\Entity\CreditNote;
use DMT\Ubl\Service\Entity\Entity;
use DMT\Ubl\Service\Entity\Components;
use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Versions;
use DMT\Ubl\Service\Event\AmountCurrencyEventSubscriber;
use DMT\Ubl\Service\Event\LegalMonetaryTotalEventSubscriber;
use DMT\Ubl\Service\Event\TaxCategoryEventSubscriber;
use DMT\Ubl\Service\Event\ElectronicAddressSchemeEventSubscriber;
use DMT\Ubl\Service\Event\InvoiceCustomizationEventSubscriber;
use DMT\Ubl\Service\Event\MandatoryDefaultsEventSubscriber;
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
     * Transform an UBL credit-note entity into a custom credit-note object.
     *
     * @param CreditNote $creditNote An UBL-CreditNote object
     * @param EntityToObjectTransformer $transformer The transformer to use
     * @return object
     */
    public function fromCreditNote(CreditNote $creditNote, EntityToObjectTransformer $transformer): object
    {
        return $transformer->transform($creditNote);
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
     * @template T
     * @param string $xml An incoming UBL-entity message to deserialize
     * @param class-string<T> $type entity-type
     * @return Entity|T
     */
    public function fromXml(string $xml, string $type): Entity
    {
        return $this->getSerializer()->deserialize($xml, $type, 'xml');
    }

    public function creditNoteFromXml(string $xml): CreditNote
    {
        return $this->fromXml($xml, CreditNote::class);
    }

    public function invoiceFromXml(string $xml): Invoice
    {
        return $this->fromXml($xml, Invoice::class);
    }

    /**
     * Transform an entity object into a UBL entity.
     *
     * @param object $object Custom representation of an entity
     * @param ObjectToEntityTransformer $transformer The transformer to use
     * @return Entity
     */
    public function toEntity(object $object, ObjectToEntityTransformer $transformer): Entity
    {
        return $transformer->transform($object);
    }

    /**
     * Transform an invoice object into a UBL Invoice.
     *
     * @param object $object Custom representation of an invoice
     * @param ObjectToEntityTransformer $transformer The transformer to use
     * @return CreditNote
     */
    public function toCreditNote(object $object, ObjectToEntityTransformer $transformer): CreditNote
    {
        $entity = $this->toEntity($object, $transformer);

        if (!$entity instanceof CreditNote) {
            throw new InvalidArgumentException("transformer failed to produce a CreditNote");
        }

        return $entity;
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
        $entity = $this->toEntity($object, $transformer);

        if (!$entity instanceof Invoice) {
            throw new InvalidArgumentException("transformer failed to produce an Invoice");
        }

        return $entity;
    }

    /**
     * Get an UBL-Invoice xml message for an Invoice.
     *
     * @param Invoice|CreditNote $entity
     * @param string $version
     *
     * @return string
     */
    public function toXml(Invoice|CreditNote $entity, string $version = Versions::DEFAULT_VERSION): string
    {
        return $this->getSerializer()->serialize($entity, 'xml', SerializationContext::create()->setVersion($version));
    }

    /**
     * @internal
     */
    private function getSerializer(): Serializer
    {
        return SerializerBuilder::create()
            ->enableEnumSupport()
            ->configureListeners(function (EventDispatcher $dispatcher) {
                $dispatcher->addSubscriber(new SkipWhenEmptyEventSubscriber());
                $dispatcher->addSubscriber(new ElectronicAddressSchemeEventSubscriber());
                $dispatcher->addSubscriber(new InvoiceCustomizationEventSubscriber());
                $dispatcher->addSubscriber(new NormalizeAddressEventSubscriber());
                $dispatcher->addSubscriber(new TaxCategoryEventSubscriber());
                $dispatcher->addSubscriber(new QuantityUnitEventSubscriber());
                $dispatcher->addSubscriber(new LegalMonetaryTotalEventSubscriber());
                $dispatcher->addSubscriber(new AmountCurrencyEventSubscriber());
                $dispatcher->addSubscriber(new MandatoryDefaultsEventSubscriber());
            })
            ->addDefaultHandlers()
            ->configureHandlers(function (HandlerRegistry $registry) {
                $registry->registerSubscribingHandler(new UnionHandler());
            })
            ->build();
    }
}
