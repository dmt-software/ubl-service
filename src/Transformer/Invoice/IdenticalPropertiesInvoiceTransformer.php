<?php

namespace DMT\Ubl\Service\Transformer\Invoice;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\AccountingCustomerParty;
use DMT\Ubl\Service\Entity\Invoice\AccountingSupplierParty;
use DMT\Ubl\Service\Entity\Invoice\Address;
use DMT\Ubl\Service\Entity\Invoice\AllowanceCharge;
use DMT\Ubl\Service\Entity\Invoice\Contact;
use DMT\Ubl\Service\Entity\Invoice\Country;
use DMT\Ubl\Service\Entity\Invoice\Delivery;
use DMT\Ubl\Service\Entity\Invoice\DeliveryLocation;
use DMT\Ubl\Service\Entity\Invoice\InvoicePeriod;
use DMT\Ubl\Service\Entity\Invoice\LegalMonetaryTotal;
use DMT\Ubl\Service\Entity\Invoice\OrderReference;
use DMT\Ubl\Service\Entity\Invoice\Party;
use DMT\Ubl\Service\Entity\Invoice\PartyIdentification;
use DMT\Ubl\Service\Entity\Invoice\PartyLegalEntity;
use DMT\Ubl\Service\Entity\Invoice\PartyName;
use DMT\Ubl\Service\Entity\Invoice\PostalAddress;
use DMT\Ubl\Service\Entity\Invoice\TaxTotal;
use DMT\Ubl\Service\Entity\Invoice\Type\AllowanceTotalAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\Amount;
use DMT\Ubl\Service\Entity\Invoice\Type\ChargeTotalAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\CompanyId;
use DMT\Ubl\Service\Entity\Invoice\Type\LineExtensionAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\PayableAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\PayableRoundingAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\PrepaidAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\TaxAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\TaxExclusiveAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\TaxInclusiveAmount;
use DMT\Ubl\Service\Helper\Invoice\AmountHelper;
use DMT\Ubl\Service\Helper\Invoice\DateTypeHelper;
use DMT\Ubl\Service\Helper\Invoice\DocumentCurrencyCodeHelper;
use DMT\Ubl\Service\Helper\Invoice\ElectronicAddressHelper;
use DMT\Ubl\Service\Helper\Invoice\IdentificationCodeHelper;
use DMT\Ubl\Service\Helper\Invoice\InvoiceTypeHelper;
use DMT\Ubl\Service\Transformer\ObjectToEntityTransformer;

class IdenticalPropertiesInvoiceTransformer implements ObjectToEntityTransformer
{
    /**
     * @inheritDoc
     */
    public function transform(object $object): Invoice
    {
        $invoice = new Invoice();
        $invoice->ublVersionId = $object->ublVersionId ?? null;
        $invoice->customizationId = $object->customizationId ?? null;
        $invoice->profileId = $object->profileId ?? null;
        $invoice->id = $object->id ?? null;
        $invoice->issueDate = DateTypeHelper::fetchFromValue($object->issueDate ?? null);
        $invoice->invoiceTypeCode = InvoiceTypeHelper::fetchFromValue($object->invoiceTypeCode ?? null);
        $invoice->taxPointDate = DateTypeHelper::fetchFromValue($object->taxPointDate ?? null);
        $invoice->documentCurrencyCode = DocumentCurrencyCodeHelper::fetchFromValue($object->documentCurrencyCode ?? null);
        $invoice->accountingCost = $object->accountingCost ?? null;
        $invoice->invoicePeriod = $this->renderInvoicePeriod($object->invoicePeriod ?? null);

        if (!empty($object->orderReference?->id)) {
            $invoice->orderReference = new OrderReference();
            $invoice->orderReference->id = $object->orderReference->id;
        }

        $invoice->accountingSupplierParty = $this->renderParty($object->accountingSupplierParty ?? null, AccountingSupplierParty::class);
        $invoice->accountingCustomerParty = $this->renderParty($object->accountingCustomerParty ?? null, AccountingCustomerParty::class);
        $invoice->delivery = $this->renderDelivery($object->delivery ?? null);
        $invoice->allowanceCharge = $this->renderAllowanceCharges($object->allowanceCharge ?? null);
        $invoice->taxTotal = $this->renderTaxTotal($object->taxTotal ?? null);
        $invoice->legalMonetaryTotal = $this->renderLegalMonetaryTotal($object->legalMonetaryTotal ?? null);

        return $invoice;
    }

    private function renderInvoicePeriod(null|object $object): ?InvoicePeriod
    {
        if ($object === null) {
            return null;
        }

        $period = new InvoicePeriod();
        $period->startDate = DateTypeHelper::fetchFromValue($object->startDate ?? null);
        $period->endDate = DateTypeHelper::fetchFromValue($object->endDate ?? null);

        return $period;
    }

    /**
     * @template T
     *
     * @param null|object $object
     * @param class-string<T> $partyType
     *
     * @return T|null
     */
    private function renderParty(null|object $object, string $partyType): ?object
    {
        if (!empty($object->party)) {
            $object = $object->party;
        }
        if (empty($object)) {
            return null;
        }

        $party = new Party();
        $party->endpointId = ElectronicAddressHelper::fetchFromValue($object->endpointId ?? null);
        $party->partyIdentification = $this->renderPartyIdentification($object->partyIdentification ?? null);

        if (!empty($object->partyName?->name)) {
            $party->partyName = new PartyName();
            $party->partyName->name = $object->partyName->name;
        }

        $party->postalAddress = $this->renderAddress($object->postalAddress ?? null, PostalAddress::class);
        $party->partyLegalEntity = $this->renderPartyLegalEntity($object->partyLegalEntity ?? null);
        $party->contact = $this->renderContact($object->contact ?? null);

        $partyType = new $partyType();
        $partyType->party = $party;

        return $partyType;
    }

    private function renderPartyIdentification(null|object $object): ?PartyIdentification
    {
        if (isset($object->id)) {
            $object = $object->id;
        }

        $identification = ElectronicAddressHelper::fetchFromValue($object);

        if ($identification) {
            $partyIdentification = new PartyIdentification();
            $partyIdentification->id = $identification;
        }

        return $partyIdentification ?? null;
    }

    private function renderAddress(null|object $object, string $addressType): ?object
    {
        if ($object === null) {
            return null;
        }

        $address = new $addressType();
        $address->streetName = $object->streetName ?? null;
        $address->additionalStreetName = $object->additionalStreetName ?? null;
        $address->buildingNumber = $object->buildingNumber ?? null;
        $address->cityName = $object->cityName ?? null;
        $address->postalZone = $object->postalZone ?? null;
        $address->country = $this->renderCountry($object->country ?? null);

        return $address;
    }

    private function renderCountry(null|string|object $object): ?Country
    {
        if (isset($object->identificationCode)) {
            $object = $object->identificationCode;
        }

        $identificationCode = IdentificationCodeHelper::fetchFromValue($object);
        if ($identificationCode) {
            $country = new Country();
            $country->identificationCode = $identificationCode;
        }

        return $country ?? null;
    }

    private function renderPartyLegalEntity(null|object $object): ?PartyLegalEntity
    {
        if ($object === null) {
            return null;
        }

        $partyLegalEntity = new PartyLegalEntity();
        $partyLegalEntity->registrationName = $object->registrationName ?? null;
        $partyLegalEntity->companyId = ElectronicAddressHelper::fetchFromValue($object->companyId ?? null, CompanyId::class);

        return $partyLegalEntity;
    }

    private function renderContact(null|object $object): ?Contact
    {
        if ($object === null) {
            return null;
        }

        $contact = new Contact();
        $contact->name = $object->name ?? null;
        $contact->telephone = $object->telephone ?? null;
        $contact->electronicMail = $object->electronicMail ?? null;

        return $contact;
    }

    private function renderDelivery(null|object $object): ?Delivery
    {
        if (!empty($object?->deliveryLocation?->address)) {
            return null;
        }

        $delivery = new Delivery();
        $delivery->deliveryLocation = new DeliveryLocation();
        $delivery->deliveryLocation->address = $this->renderAddress($object->deliveryLocation->address, Address::class);

        return $delivery;
    }

    private function renderAllowanceCharges(null|object|array $objects): ?array
    {
        if ($objects === null) {
            return null;
        }

        if (is_object($objects)) {
            $objects = [$objects];
        }

        return array_filter(array_map($this->renderAllowanceCharge(...), $objects));
    }

    private function renderAllowanceCharge(null|object $object): ?AllowanceCharge
    {
        if ($object === null) {
            return null;
        }

        $allowanceCharge = new AllowanceCharge();
        $allowanceCharge->allowanceChargeReason = $object->allowanceChargeReason ?? null;
        $allowanceCharge->amount = AmountHelper::fetchFromValue($object->amount ?? null, Amount::class);
        $allowanceCharge->chargeIndicator = $object->chargeIndicator ?? null;
        $allowanceCharge->taxCategory = $object->taxCategory ?? null;

        return $allowanceCharge;
    }

    private function renderTaxTotal(null|object $object): ?TaxTotal
    {
        if ($object?->taxAmount === null) {
            return null;
        }

        $taxTotal = new TaxTotal();
        $taxTotal->taxAmount = AmountHelper::fetchFromValue($object->taxAmount ?? null, TaxAmount::class);

        return $taxTotal;
    }

    private function renderLegalMonetaryTotal(null|object $object): ?LegalMonetaryTotal
    {
        if (empty($object)) {
            return null;
        }

        $legalMonetaryTotal = new LegalMonetaryTotal();
        $legalMonetaryTotal->lineExtensionAmount = AmountHelper::fetchFromValue($object->lineExtensionAmount ?? null, LineExtensionAmount::class);
        $legalMonetaryTotal->taxExclusiveAmount = AmountHelper::fetchFromValue($object->taxExclusiveAmount ?? null, TaxExclusiveAmount::class);
        $legalMonetaryTotal->taxInclusiveAmount = AmountHelper::fetchFromValue($object->taxInclusiveAmount ?? null, TaxInclusiveAmount::class);
        $legalMonetaryTotal->allowanceTotalAmount = AmountHelper::fetchFromValue($object->allowanceTotalAmount ?? null, AllowanceTotalAmount::class);
        $legalMonetaryTotal->chargeTotalAmount = AmountHelper::fetchFromValue($object->chargeTotalAmount ?? null, ChargeTotalAmount::class);
        $legalMonetaryTotal->prepaidAmount = AmountHelper::fetchFromValue($object->prepaidAmount ?? null, PrepaidAmount::class);
        $legalMonetaryTotal->payableAmount = AmountHelper::fetchFromValue($object->payableAmount ?? null, PayableAmount::class);
        $legalMonetaryTotal->payableRoundingAmount = AmountHelper::fetchFromValue($object->payableRoundingAmount ?? null, PayableRoundingAmount::class);

        return $legalMonetaryTotal;
    }
}
