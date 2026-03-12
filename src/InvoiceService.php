<?php

namespace DMT\Ubl\Service;

use DMT\Ubl\Service\Entity\CreditNote;
use DMT\Ubl\Service\Entity\Document;
use DMT\Ubl\Service\Entity\Entity;
use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Versions;
use DMT\Ubl\Service\Event\AmountCurrencyEventSubscriber;
use DMT\Ubl\Service\Event\ElectronicAddressSchemeEventSubscriber;
use DMT\Ubl\Service\Event\InvoiceCustomizationEventSubscriber;
use DMT\Ubl\Service\Event\LegalMonetaryTotalEventSubscriber;
use DMT\Ubl\Service\Event\MandatoryDefaultsEventSubscriber;
use DMT\Ubl\Service\Event\NormalizeAddressEventSubscriber;
use DMT\Ubl\Service\Event\QuantityUnitEventSubscriber;
use DMT\Ubl\Service\Event\SkipWhenEmptyEventSubscriber;
use DMT\Ubl\Service\Event\TaxCategoryEventSubscriber;
use DMT\Ubl\Service\Handler\UnionHandler;
use DMT\Ubl\Service\List\ElectronicAddressScheme;
use DMT\Ubl\Service\Transformer\DocumentToObjectTransformer;
use DMT\Ubl\Service\Transformer\ObjectToDocumentTransformer;
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
     * Transform an UBL document into a custom object.
     *
     * @param Document $document An UBL-Document object
     * @param DocumentToObjectTransformer $transformer The transformer to use
     * @return object
     */
    public function fromDocument(Document $document, DocumentToObjectTransformer $transformer): object
    {
        return $transformer->transform($document);
    }

    /**
     * Transform an UBL credit-note document into a custom credit-note object.
     *
     * @param CreditNote $creditNote An UBL-CreditNote object
     * @param DocumentToObjectTransformer $transformer The transformer to use
     * @return object
     */
    public function fromCreditNote(CreditNote $creditNote, DocumentToObjectTransformer $transformer): object
    {
        return $this->fromDocument($creditNote, $transformer);
    }

    /**
     * Transform an UBL invoice document into a custom invoice object.
     *
     * @param Invoice $invoice An UBL-Invoice object
     * @param DocumentToObjectTransformer $transformer The transformer to use
     * @return object
     */
    public function fromInvoice(Invoice $invoice, DocumentToObjectTransformer $transformer): object
    {
        return $this->fromDocument($invoice, $transformer);
    }

    /**
     * Get an object representation of an UBL-document XML message.
     *
     * @template T
     * @param string $xml An incoming UBL-document message to deserialize
     * @param class-string<T> $type document-type
     * @return Document|T
     */
    public function fromXml(string $xml, string $type): Document
    {
        return $this->getSerializer()->deserialize($xml, $type, 'xml');
    }

    /**
     * Get an object representation of an UBL-credit-note XML message.
     *
     * @param string $xml
     * @return CreditNote
     */
    public function creditNoteFromXml(string $xml): CreditNote
    {
        return $this->fromXml($xml, CreditNote::class);
    }

    /**
     * Get an object representation of an UBL-invoice XML message.
     *
     * @param string $xml
     * @return Invoice
     */
    public function invoiceFromXml(string $xml): Invoice
    {
        return $this->fromXml($xml, Invoice::class);
    }

    /**
     * Transform an entity object into a UBL entity.
     *
     * @param object $object Custom representation of an entity
     * @param ObjectToDocumentTransformer $transformer The transformer to use
     * @return Document
     */
    public function toDocument(object $object, ObjectToDocumentTransformer $transformer): Document
    {
        return $transformer->transform($object);
    }

    /**
     * Transform an invoice object into a UBL Invoice.
     *
     * @param object $object Custom representation of an invoice
     * @param ObjectToDocumentTransformer $transformer The transformer to use
     * @return CreditNote
     */
    public function toCreditNote(object $object, ObjectToDocumentTransformer $transformer): CreditNote
    {
        $entity = $this->toDocument($object, $transformer);

        if (!$entity instanceof CreditNote) {
            throw new InvalidArgumentException("transformer failed to produce a CreditNote");
        }

        return $entity;
    }

    /**
     * Transform an invoice object into a UBL Invoice.
     *
     * @param object $object Custom representation of an invoice
     * @param ObjectToDocumentTransformer $transformer The transformer to use
     * @return Invoice
     */
    public function toInvoice(object $object, ObjectToDocumentTransformer $transformer): Invoice
    {
        $entity = $this->toDocument($object, $transformer);

        if (!$entity instanceof Invoice) {
            throw new InvalidArgumentException("transformer failed to produce an Invoice");
        }

        return $entity;
    }

    /**
     * Get an UBL-entity XML message for an Entity.
     *
     * @param Entity $entity
     * @param string $version
     *
     * @return string
     */
    public function toXml(Entity $entity, string $version = Versions::DEFAULT_VERSION): string
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
