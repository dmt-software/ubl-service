<?php

namespace DMT\Ubl\Service\Entity;

use DateTime;
use DMT\Ubl\Service\Entity\Invoice\AccountingCustomerParty;
use DMT\Ubl\Service\Entity\Invoice\AccountingSupplierParty;
use DMT\Ubl\Service\Entity\Invoice\AllowanceCharge;
use DMT\Ubl\Service\Entity\Invoice\Delivery;
use DMT\Ubl\Service\Entity\Invoice\InvoicePeriod;
use DMT\Ubl\Service\Entity\Invoice\LegalMonetaryTotal;
use DMT\Ubl\Service\Entity\Invoice\OrderReference;
use DMT\Ubl\Service\Entity\Invoice\PaymentTerms;
use DMT\Ubl\Service\Entity\Invoice\TaxTotal;
use DMT\Ubl\Service\Entity\Invoice\Type\DocumentCurrencyCode;
use DMT\Ubl\Service\Entity\Invoice\Type\InvoiceTypeCode;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Until;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlNamespace;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(name: "Invoice", namespace: "urn:oasis:names:specification:ubl:schema:xsd:Invoice-2")]
#[XmlNamespace(uri: "urn:oasis:names:specification:ubl:schema:xsd:Invoice-2", prefix: "")]
#[XmlNamespace(uri: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2", prefix: "cac")]
#[XmlNamespace(uri: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2", prefix: "cbc")]
class Invoice implements Entity
{
    public const string DEFAULT_VERSION = self::VERSION_2_0;
    public const string VERSION_1_0 = '1.0';
    public const string VERSION_1_1 = '1.1';
    public const string VERSION_1_2 = '1.2';
    public const string VERSION_2_0 = '2.0';
    public const string VERSION_NLCIUS = '2.0.0-nlcius';

    #[SerializedName(name: "UBLVersionID")]
    #[Until(version: self::VERSION_1_2)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $ublVersionId = null;

    #[SerializedName(name: "CustomizationID")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $customizationId = null;

    #[SerializedName(name: "ProfileID")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $profileId = null;

    #[SerializedName(name: "ID")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $id = null;

    #[SerializedName(name: "IssueDate")]
    #[Type(name: "DateTime<'Y-m-d'>")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|DateTime $issueDate = null;

    #[SerializedName(name: "DueDate")]
    #[Type(name: "DateTime<'Y-m-d'>")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|DateTime $dueDate = null;

    #[SerializedName(name: "InvoiceTypeCode")]
    #[Type(name: InvoiceTypeCode::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|InvoiceTypeCode $invoiceTypeCode = null;

    #[SerializedName(name: "TaxPointDate")]
    #[Since(version: self::VERSION_1_1)]
    #[Type(name: "DateTime<'Y-m-d'>")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|DateTime $taxPointDate = null;

    #[SerializedName(name: "DocumentCurrencyCode")]
    #[Type(name: DocumentCurrencyCode::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|DocumentCurrencyCode $documentCurrencyCode = null;

    #[SerializedName(name: "AccountingCost")]
    #[Since(version: self::VERSION_1_1)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $accountingCost = null;

    #[SerializedName(name: "InvoicePeriod")]
    #[Since(version: self::VERSION_1_1)]
    #[Type(name: InvoicePeriod::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|InvoicePeriod $invoicePeriod = null;

    #[SerializedName(name: "OrderReference")]
    #[Since(version: self::VERSION_1_1)]
    #[Type(name: OrderReference::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|OrderReference $orderReference = null;

    #[SerializedName(name: "AccountingSupplierParty")]
    #[Type(name: AccountingSupplierParty::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|AccountingSupplierParty $accountingSupplierParty = null;

    #[SerializedName(name: "AccountingCustomerParty")]
    #[Type(name: AccountingCustomerParty::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|AccountingCustomerParty $accountingCustomerParty = null;

    #[SerializedName(name: "Delivery")]
    #[Since(version: self::VERSION_1_1)]
    #[Type(name: Delivery::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|Delivery $delivery = null;

    #[SerializedName(name: "PaymentTerms")]
    #[Type(name: PaymentTerms::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|PaymentTerms $paymentTerms = null;

    #[Type(name: "array<DMT\Ubl\Service\Entity\Invoice\AllowanceCharge>")]
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

    #[Type(name: "array<DMT\Ubl\Service\Entity\InvoiceLine>")]
    #[XmlList(
        entry: "InvoiceLine",
        inline: true,
        namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
    )]
    /** @var array<InvoiceLine> $invoiceLine */
    public null|array $invoiceLine = null;
}
