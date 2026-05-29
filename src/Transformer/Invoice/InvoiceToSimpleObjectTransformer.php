<?php

namespace DMT\Ubl\Service\Transformer\Invoice;

use Closure;
use DateTime;
use DMT\Ubl\Service\Entity\Document as UBLDocument;
use DMT\Ubl\Service\Entity\Invoice as UBLInvoice;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Address as UBLAddress;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Party as UBLParty;
use DMT\Ubl\Service\Objects\Address;
use DMT\Ubl\Service\Objects\CompanyId;
use DMT\Ubl\Service\Objects\Invoice;
use DMT\Ubl\Service\Objects\Party;
use DMT\Ubl\Service\Objects;
use DMT\Ubl\Service\Transformer\DocumentToObjectTransformer;
use InvalidArgumentException;

class InvoiceToSimpleObjectTransformer implements DocumentToObjectTransformer
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
    public function transform(UBLDocument $document): Objects\Invoice
    {
        if (!$document instanceof UBLInvoice) {
            throw new InvalidArgumentException('Expected instance of ' . UBLDocument::class);
        }

        $invoice = new Invoice(documentId: $document->id);
        $invoice->invoiceDate = $document->issueDate ?? new DateTime();
        $invoice->dueDate = $document->dueDate;
        $invoice->invoiceType = $document?->invoiceTypeCode?->code?->value;
        $invoice->orderReference = $document?->orderReference->id;
        $invoice->salesOrderReference = $document?->orderReference->salesOrderId;
        $invoice->invoicePeriod = array_filter([
            $document?->invoicePeriod?->startDate,
            $document?->invoicePeriod?->endDate,
        ]) ?: null;
        $invoice->paymentTerm = $document?->paymentTerms?->note;
        $invoice->total = $document?->legalMonetaryTotal?->payableAmount->amount;

        if ($document->accountingSupplierParty && $document->accountingSupplierParty->party) {
            $invoice->seller = $this->renderParty($document->accountingSupplierParty->party);
        }

        if ($document->accountingCustomerParty && $document->accountingCustomerParty->party) {
            $invoice->buyer = $this->renderParty($document->accountingCustomerParty->party);
        }

        if ($document?->delivery?->deliveryLocation?->address) {
            $invoice->address = $this->renderDeliveryAddress($document->delivery->deliveryLocation->address);
        }

        if ($document?->paymentMeans) {
            foreach ($document->paymentMeans as $paymentMeans) {
                if ($paymentMeans->payeeFinancialAccount?->id?->id) {
                    $invoice->bankAccountNumber = $paymentMeans->payeeFinancialAccount->id->id;
                    break;
                }
            }
        }

        if (isset($this->invoiceLineCallback)) {
            $invoice->invoiceLines = array_map($this->invoiceLineCallback, $document->invoiceLine);
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
