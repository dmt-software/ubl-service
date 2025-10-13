<?php

namespace DMT\Ubl\Service\Builder;

use DateTime;
use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\AccountingCustomerParty;
use DMT\Ubl\Service\Entity\Invoice\AccountingSupplierParty;
use DMT\Ubl\Service\Entity\Invoice\Address;
use DMT\Ubl\Service\Entity\Invoice\ClassifiedTaxCategory;
use DMT\Ubl\Service\Entity\Invoice\Contact;
use DMT\Ubl\Service\Entity\Invoice\Country;
use DMT\Ubl\Service\Entity\Invoice\Delivery;
use DMT\Ubl\Service\Entity\Invoice\DeliveryLocation;
use DMT\Ubl\Service\Entity\Invoice\InvoicePeriod;
use DMT\Ubl\Service\Entity\Invoice\Item;
use DMT\Ubl\Service\Entity\Invoice\LegalMonetaryTotal;
use DMT\Ubl\Service\Entity\Invoice\OrderReference;
use DMT\Ubl\Service\Entity\Invoice\Party;
use DMT\Ubl\Service\Entity\Invoice\PartyLegalEntity;
use DMT\Ubl\Service\Entity\Invoice\PartyTaxScheme;
use DMT\Ubl\Service\Entity\Invoice\PaymentTerms;
use DMT\Ubl\Service\Entity\Invoice\PostalAddress;
use DMT\Ubl\Service\Entity\Invoice\Price;
use DMT\Ubl\Service\Entity\Invoice\SellersItemIdentification;
use DMT\Ubl\Service\Entity\Invoice\StandardItemIdentification;
use DMT\Ubl\Service\Entity\Invoice\TaxScheme;
use DMT\Ubl\Service\Entity\Invoice\Type\BaseQuantity;
use DMT\Ubl\Service\Entity\Invoice\Type\CompanyId;
use DMT\Ubl\Service\Entity\Invoice\Type\Id;
use DMT\Ubl\Service\Entity\Invoice\Type\InvoicedQuantity;
use DMT\Ubl\Service\Entity\Invoice\Type\LineExtensionAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\PayableAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\PriceAmount;
use DMT\Ubl\Service\Entity\InvoiceLine;
use DMT\Ubl\Service\Helper\Invoice\AmountHelper;
use DMT\Ubl\Service\Helper\Invoice\ElectronicAddressHelper;
use DMT\Ubl\Service\Helper\Invoice\IdentificationCodeHelper;
use DMT\Ubl\Service\Helper\Invoice\InvoiceTypeHelper;
use DMT\Ubl\Service\Helper\Invoice\QuantityHelper;

class InvoiceBuilder
{
    private Invoice $invoice;

    /** @param array<int, DateTime>|null $invoicePeriod */
    public function __construct(
        int $documentId,
        null|DateTime $invoiceDate = null,
        null|DateTime $dueDate = null,
        null|string $invoiceType = null,
        null|string $orderReference = null,
        null|string $salesOrderReference = null,
        null|string $paymentTerm = null,
        null|array $invoicePeriod = null,
        float $total = 0.0
    ) {
        $this->invoice = new Invoice();
        $this->invoice->id = $documentId;
        $this->invoice->issueDate = $invoiceDate ?? new DateTime();
        $this->invoice->dueDate = $dueDate;
        $this->invoice->invoiceTypeCode = InvoiceTypeHelper::fetchFromValue($invoiceType);
        $this->invoice->orderReference = new OrderReference();
        $this->invoice->orderReference->id = $orderReference;
        $this->invoice->orderReference->salesOrderId = $salesOrderReference;
        $this->invoice->legalMonetaryTotal = new LegalMonetaryTotal();
        $this->invoice->legalMonetaryTotal->payableAmount = AmountHelper::fetchFromValue($total, PayableAmount::class);

        if ($invoicePeriod) {
            $this->invoice->invoicePeriod = new InvoicePeriod();
            $this->invoice->invoicePeriod->startDate = min($invoicePeriod);
            $this->invoice->invoicePeriod->endDate = max($invoicePeriod);
        }

        if ($paymentTerm) {
            $this->invoice->paymentTerms = new PaymentTerms();
            $this->invoice->paymentTerms->note = $paymentTerm;
        }
    }

    public function withAccountSupplierParty(
        object $identification,
        string $companyName,
        string $address,
        string $postcode,
        string $city,
        string $country,
        null|string $vatNumber = null,
        null|string $companyLegalName = null,
        null|string $contact = null,
        null|string $phone = null,
        null|string $email = null,
    ): self {
        $this->invoice->accountingSupplierParty = new AccountingSupplierParty();
        $this->invoice->accountingSupplierParty->party = $this->getParty(...func_get_args());

        return $this;
    }

    public function withAccountingCustomerParty(
        object $identification,
        string $companyName,
        string $address,
        string $postcode,
        string $city,
        string $country,
        null|string $vatNumber = null,
        null|string $companyLegalName = null,
        null|string $contact = null,
        null|string $phone = null,
        null|string $email = null,
    ): self {
        $this->invoice->accountingCustomerParty = new AccountingCustomerParty();
        $this->invoice->accountingCustomerParty->party = $this->getParty(...func_get_args());

        return $this;
    }

    public function withDelivery(
        string $address,
        string $postcode,
        string $city,
        string $country,
    ): self {
        $deliveryAddress = new Address();
        $deliveryAddress->streetName = $address;
        $deliveryAddress->cityName = $city;
        $deliveryAddress->postalZone = $postcode;
        $deliveryAddress->country = new Country();
        $deliveryAddress->country->identificationCode = IdentificationCodeHelper::fetchFromValue($country);;

        $this->invoice->delivery = new Delivery();
        $this->invoice->delivery->deliveryLocation = new DeliveryLocation();
        $this->invoice->delivery->deliveryLocation->address = $deliveryAddress;

        return $this;
    }

    public function addInvoiceLine(
        int $lineNumber,
        string $productName,
        int $amount,
        float $price,
        int $vatPercentage,
        null|string $sku = null,
        null|string $ean = null,
    ): self {
        $invoiceLine = new InvoiceLine();
        $invoiceLine->id = $lineNumber;
        $invoiceLine->invoicedQuantity = QuantityHelper::fetchFromValue($amount, InvoicedQuantity::class);
        $invoiceLine->lineExtensionAmount = AmountHelper::fetchFromValue($price * $amount, LineExtensionAmount::class);
        $invoiceLine->item = new Item();
        $invoiceLine->item->name = $productName;
        $invoiceLine->item->sellersItemIdentification = new SellersItemIdentification();
        $invoiceLine->item->sellersItemIdentification->id = ElectronicAddressHelper::fetchFromValue($sku, Id::class);
        $invoiceLine->item->classifiedTaxCategory = new ClassifiedTaxCategory();
        $invoiceLine->item->classifiedTaxCategory->percent = $vatPercentage;
        $invoiceLine->price = new Price();
        $invoiceLine->price->priceAmount = AmountHelper::fetchFromValue($price, PriceAmount::class);
        $invoiceLine->price->baseQuantity = QuantityHelper::fetchFromValue(1, BaseQuantity::class);

        if ($ean !== null) {
            $id = (object)['id' => $ean, 'schemeId' => 'GTIN'];
            $invoiceLine->item->standardItemIdentification = new StandardItemIdentification();
            $invoiceLine->item->standardItemIdentification->id = ElectronicAddressHelper::fetchFromValue($id, Id::class);
        }

        $this->invoice->invoiceLine[] = $invoiceLine;

        return $this;
    }

    public function build(): Invoice
    {
        return $this->invoice;
    }

    protected function getParty(
        object $identification,
        string $companyName,
        string $address,
        string $postcode,
        string $city,
        string $country,
        null|string $vatNumber = null,
        null|string $companyLegalName = null,
        null|string $contact = null,
        null|string $phone = null,
        null|string $email = null,
    ): Party {
        $party = new Party();
        $party->endpointId = ElectronicAddressHelper::fetchFromValue($identification);
        $party->postalAddress = new PostalAddress();
        $party->postalAddress->streetName = $address;
        $party->postalAddress->cityName = $city;
        $party->postalAddress->postalZone = $postcode;
        $party->postalAddress->country = new Country();
        $party->postalAddress->country->identificationCode = IdentificationCodeHelper::fetchFromValue($country);
        $party->partyLegalEntity = new PartyLegalEntity();
        $party->partyLegalEntity->registrationName = $companyLegalName ?? $companyName;

        if ($vatNumber) {
            $party->partyTaxScheme = new PartyTaxScheme();
            $party->partyTaxScheme->companyId = ElectronicAddressHelper::fetchFromValue($vatNumber, CompanyId::class);
            $party->partyTaxScheme->taxScheme = new TaxScheme();
            $party->partyTaxScheme->taxScheme->id = ElectronicAddressHelper::fetchFromValue('VAT', Id::class);
        }

        if ($email || $phone || $contact) {
            $party->contact = new Contact();
            $party->contact->name = $contact;
            $party->contact->telephone = $phone;
            $party->contact->electronicMail = $email;
        }

        return $party;
    }
}
