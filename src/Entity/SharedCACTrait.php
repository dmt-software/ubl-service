<?php

namespace DMT\Ubl\Service\Entity;

use DMT\Ubl\Service\Entity\Components\AccountingCustomerParty;
use DMT\Ubl\Service\Entity\Components\AccountingSupplierParty;
use DMT\Ubl\Service\Entity\Components\AllowanceCharge;
use DMT\Ubl\Service\Entity\Components\Delivery;
use DMT\Ubl\Service\Entity\Components\InvoicePeriod;
use DMT\Ubl\Service\Entity\Components\LegalMonetaryTotal;
use DMT\Ubl\Service\Entity\Components\OrderReference;
use DMT\Ubl\Service\Entity\Components\PaymentMeans;
use DMT\Ubl\Service\Entity\Components\PaymentTerms;
use DMT\Ubl\Service\Entity\Components\TaxTotal;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlList;

trait SharedCACTrait
{
    #[SerializedName(name: "InvoicePeriod")]
    #[Since(version: Versions::VERSION_1_1)]
    #[Type(name: InvoicePeriod::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|InvoicePeriod $invoicePeriod = null;

    #[SerializedName(name: "OrderReference")]
    #[Since(version: Versions::VERSION_1_1)]
    #[Type(name: OrderReference::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|OrderReference $orderReference = null;

    // BillingReference
    // DespatchDocumentReference
    // ReceiptDocumentReference
    // OriginatorDocumentReference
    // ContractDocumentReference
    // AdditionalDocumentReference
    // ProjectReference

    #[SerializedName(name: "AccountingSupplierParty")]
    #[Type(name: AccountingSupplierParty::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|AccountingSupplierParty $accountingSupplierParty = null;

    #[SerializedName(name: "AccountingCustomerParty")]
    #[Type(name: AccountingCustomerParty::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|AccountingCustomerParty $accountingCustomerParty = null;

    // PayeeParty
    // TaxRepresentativeParty

    #[SerializedName(name: "Delivery")]
    #[Since(version: Versions::VERSION_1_1)]
    #[Type(name: Delivery::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|Delivery $delivery = null;

    #[Type(name: 'array<' . PaymentMeans::class . '>')]
    #[XmlList(
        entry: "PaymentMeans",
        inline: true,
        namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
    )]
    /** @var array<PaymentMeans> $paymentMeans */
    public null|array $paymentMeans = null;

    #[SerializedName(name: "PaymentTerms")]
    #[Type(name: PaymentTerms::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|PaymentTerms $paymentTerms = null;

    #[Type(name: 'array<' . AllowanceCharge::class . '>')]
    #[XmlList(
        entry: "AllowanceCharge",
        inline: true,
        namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
    )]
    /** @var array<AllowanceCharge> $allowanceCharge */
    public null|array $allowanceCharge = null;

    #[SerializedName(name: "TaxTotal")]
    #[Type(name: TaxTotal::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|TaxTotal $taxTotal = null;

    #[SerializedName(name: "LegalMonetaryTotal")]
    #[Type(name: LegalMonetaryTotal::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|LegalMonetaryTotal $legalMonetaryTotal = null;
}
