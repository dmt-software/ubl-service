<?php

namespace DMT\Ubl\Service\Transformer\Invoice;

use Closure;
use DMT\Ubl\Service\Entity\Entity;
use DMT\Ubl\Service\Entity\Invoice as UBLInvoice;
use DMT\Ubl\Service\Entity\Invoice\Address as UBLAddress;
use DMT\Ubl\Service\Entity\Invoice\Party as UBLParty;
use DMT\Ubl\Service\Objects\Address;
use DMT\Ubl\Service\Objects\CompanyId;
use DMT\Ubl\Service\Objects\Invoice;
use DMT\Ubl\Service\Objects\Party;
use DMT\Ubl\Service\Transformer\EntityToObjectTransformer;
use InvalidArgumentException;

class InvoiceToSimpleObjectTransformer implements EntityToObjectTransformer
{
    private Closure $invoiceLineCallback;

    public function __construct(bool $transformInvoiceLines = true)
    {
        if ($transformInvoiceLines) {
            $this->invoiceLineCallback = (new InvoiceLineToSimpleObjectTransformer())->transform(...);
        }
    }

    /**
     * @inheritDoc
     */
    public function transform(Entity|UBLInvoice $entity): Invoice
    {
        if (!$entity instanceof UBLInvoice) {
            throw new InvalidArgumentException('Expected instance of ' . UBLInvoice::class);
        }

        $invoice = new Invoice(documentId: $entity->id);
        $invoice->invoiceDate = $entity->issueDate;
        $invoice->dueDate = $entity->dueDate;
        $invoice->invoiceType = $entity?->invoiceTypeCode?->code?->value;
        $invoice->orderReference = $entity?->orderReference->id;
        $invoice->salesOrderReference = $entity?->orderReference->salesOrderId;
        $invoice->invoicePeriod = array_filter([
            $entity?->invoicePeriod?->startDate,
            $entity?->invoicePeriod?->endDate,
        ]) ?: null;
        $invoice->paymentTerm = $entity?->paymentTerms?->note;
        $invoice->total = $entity?->legalMonetaryTotal?->payableAmount->amount;

        if ($entity->accountingSupplierParty) {
            $invoice->seller = $this->renderParty($entity->accountingSupplierParty);
        }

        if ($entity->accountingCustomerParty) {
            $invoice->buyer = $this->renderParty($entity->accountingCustomerParty);
        }

        if ($entity?->delivery?->deliveryLocation?->address) {
            $invoice->address = $this->renderDeliveryAddress($entity->delivery->deliveryLocation->address);
        }

        if (isset($this->invoiceLineCallback)) {
            $invoice->invoiceLines = array_map($this->invoiceLineCallback, $entity->invoiceLine);
        }

        return $invoice;
    }

    private function renderParty(UBLParty $entity): Party
    {
        $identification = new CompanyId(id: '', schemeId: '');
        if ($entity->endpointId?->id) {
            $identification->id = $entity->endpointId->id;
            $identification->schemeId = $entity->endpointId->schemeId ?? 'ZZZ';
        } elseif ($entity?->partyTaxScheme?->companyId?->id) {
            $identification->id = $entity->partyTaxScheme->companyId->id;
            $identification->schemeId = $entity->partyTaxScheme->companyId->schemeId ?? 'ZZZ';
        }

        if (!$identification->id && !$entity?->partyName?->name) {
            throw new InvalidArgumentException('No identification or name for party found');
        }

        $party = new Party(
            identification: $identification,
            companyName: $entity->partyName->name ?? '',
            address: $entity?->postalAddress->streetName ?? '',
            postcode: $entity?->postalAddress->postalZone ?? '',
            city: $entity?->postalAddress->cityName ?? '',
            country: $entity?->postalAddress?->country?->identificationCode->code ?? '',
        );

        if ($party->address && !str_ends_with($party->address, $entity?->postalAddress->buildingNumber)) {
            $party->address .= ' '  . $entity?->postalAddress->buildingNumber;
        }

        $party->companyLegalName = $entity?->partyLegalEntity->registrationName;
        $party->vatNumber = $entity?->partyTaxScheme?->companyId?->id;
        $party->contact = $entity?->contact->name;
        $party->email = $entity?->contact->electronicMail;
        $party->phone = $entity?->contact->telephone;

        return $party;
    }

    private function renderDeliveryAddress(UBLAddress $entity): Address
    {
        $address = new Address(
            address: $entity->streetName ?? '',
            postcode: $entity->postalZone ?? '',
            city: $entity->cityName ?? '',
            country: $entity->country?->identificationCode->code ?? '',
        );

        if ($address->address && !str_ends_with($address->address, $entity?->buildingNumber)) {
            $address->address .= ' '  . $entity->buildingNumber;
        }

        return $address;
    }
}
