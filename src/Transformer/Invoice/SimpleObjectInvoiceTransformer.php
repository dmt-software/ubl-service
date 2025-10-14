<?php

namespace DMT\Ubl\Service\Transformer\Invoice;

use Closure;
use DateTime;
use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\AccountingCustomerParty;
use DMT\Ubl\Service\Entity\Invoice\AccountingSupplierParty;
use DMT\Ubl\Service\Entity\Invoice\Address;
use DMT\Ubl\Service\Entity\Invoice\Contact;
use DMT\Ubl\Service\Entity\Invoice\Country;
use DMT\Ubl\Service\Entity\Invoice\Delivery;
use DMT\Ubl\Service\Entity\Invoice\DeliveryLocation;
use DMT\Ubl\Service\Entity\Invoice\InvoicePeriod;
use DMT\Ubl\Service\Entity\Invoice\LegalMonetaryTotal;
use DMT\Ubl\Service\Entity\Invoice\OrderReference;
use DMT\Ubl\Service\Entity\Invoice\Party;
use DMT\Ubl\Service\Entity\Invoice\PartyLegalEntity;
use DMT\Ubl\Service\Entity\Invoice\PartyTaxScheme;
use DMT\Ubl\Service\Entity\Invoice\PaymentTerms;
use DMT\Ubl\Service\Entity\Invoice\PostalAddress;
use DMT\Ubl\Service\Entity\Invoice\TaxScheme;
use DMT\Ubl\Service\Entity\Invoice\Type\CompanyId;
use DMT\Ubl\Service\Entity\Invoice\Type\Id;
use DMT\Ubl\Service\Entity\Invoice\Type\PayableAmount;
use DMT\Ubl\Service\Helper\Invoice\AmountHelper;
use DMT\Ubl\Service\Helper\Invoice\ElectronicAddressHelper;
use DMT\Ubl\Service\Helper\Invoice\IdentificationCodeHelper;
use DMT\Ubl\Service\Helper\Invoice\InvoiceTypeHelper;
use DMT\Ubl\Service\Objects\Address as AddressDTO;
use DMT\Ubl\Service\Objects\Invoice as InvoiceDTO;
use DMT\Ubl\Service\Objects\Party as PartyDTO;
use DMT\Ubl\Service\Transformer\ObjectToEntityTransformer;
use InvalidArgumentException;

class SimpleObjectInvoiceTransformer implements ObjectToEntityTransformer
{
    private Closure $invoiceLineCallback;

    public function __construct(bool $transformInvoiceLines = true)
    {
        if ($transformInvoiceLines) {
            $this->invoiceLineCallback = (new SimpleObjectInvoiceLineTransformer())->transform(...);
        }
    }

    public function transform(object $object): Invoice
    {
        if (!$object instanceof InvoiceDTO) {
            throw new InvalidArgumentException('Expected instance of ' . InvoiceDTO::class);
        }

        $invoice = new Invoice();
        $invoice->id = $object->documentId;
        $invoice->issueDate = $object->invoiceDate ?? new DateTime();
        $invoice->dueDate = $object->dueDate;
        $invoice->invoiceTypeCode = InvoiceTypeHelper::fetchFromValue($object->invoiceType);
        $invoice->orderReference = new OrderReference();
        $invoice->orderReference->id = $object->orderReference;
        $invoice->orderReference->salesOrderId = $object->salesOrderReference;
        $invoice->legalMonetaryTotal = new LegalMonetaryTotal();
        $invoice->legalMonetaryTotal->payableAmount = AmountHelper::fetchFromValue($object->total, PayableAmount::class);

        if ($object->invoicePeriod) {
            $invoice->invoicePeriod = new InvoicePeriod();
            $invoice->invoicePeriod->startDate = min($object->invoicePeriod);
            $invoice->invoicePeriod->endDate = max($object->invoicePeriod);
        }

        if ($object->paymentTerm) {
            $invoice->paymentTerms = new PaymentTerms();
            $invoice->paymentTerms->note = $object->paymentTerm;
        }

        $invoice->accountingSupplierParty = $this->renderParty($object->seller ?? null, AccountingSupplierParty::class);
        $invoice->accountingCustomerParty = $this->renderParty($object->buyer ?? null, AccountingCustomerParty::class);

        if (isset($this->invoiceLineCallback)) {
            $invoice->invoiceLine = array_map($this->invoiceLineCallback, $object->invoiceLines);
        }

        return $invoice;
    }

    /**
     * @template T
     *
     * @param null|PartyDTO $object
     * @param class-string<T> $partyType
     *
     * @return T|null
     */
    private function renderParty(null|PartyDTO $object, string $partyType): ?object
    {
        if (!$object instanceof PartyDTO) {
            return null;
        }

        $party = new Party();
        $party->endpointId = ElectronicAddressHelper::fetchFromValue($object->identification);
        $party->postalAddress = new PostalAddress();
        $party->postalAddress->streetName = $object->address;
        $party->postalAddress->cityName = $object->city;
        $party->postalAddress->postalZone = $object->postcode;
        $party->postalAddress->country = new Country();
        $party->postalAddress->country->identificationCode = IdentificationCodeHelper::fetchFromValue($object->country);
        $party->partyLegalEntity = new PartyLegalEntity();
        $party->partyLegalEntity->registrationName = $object->companyLegalName ?? $object->companyName;

        if ($object->vatNumber) {
            $party->partyTaxScheme = new PartyTaxScheme();
            $party->partyTaxScheme->companyId = ElectronicAddressHelper::fetchFromValue($object->vatNumber, CompanyId::class);
            $party->partyTaxScheme->taxScheme = new TaxScheme();
            $party->partyTaxScheme->taxScheme->id = ElectronicAddressHelper::fetchFromValue('VAT', Id::class);
        }

        if ($object->email || $object->phone || $object->contact) {
            $party->contact = new Contact();
            $party->contact->name = $object->contact;
            $party->contact->telephone = $object->phone;
            $party->contact->electronicMail = $object->email;
        }

        $entity = new $partyType();
        $entity->party = $party;

        return $entity;
    }

    private function renderDelivery(null|AddressDTO $object): ?Delivery
    {
        if (!$object instanceof AddressDTO) {
            return null;
        }

        $deliveryAddress = new Address();
        $deliveryAddress->streetName = $object->address;
        $deliveryAddress->cityName = $object->city;
        $deliveryAddress->postalZone = $object->postcode;
        $deliveryAddress->country = new Country();
        $deliveryAddress->country->identificationCode = IdentificationCodeHelper::fetchFromValue($object->country);

        $delivery = new Delivery();
        $delivery->deliveryLocation = new DeliveryLocation();
        $delivery->deliveryLocation->address = $deliveryAddress;

        return $delivery;
    }
}
