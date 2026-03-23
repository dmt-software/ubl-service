<?php

namespace DMT\Ubl\Service\Entity;

use DateTime;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\AccountingCustomerParty;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\AccountingSupplierParty;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\AdditionalDocumentReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\AllowanceCharge;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\BillingReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\BuyerCustomerParty;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\ContractDocumentReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Delivery;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\DeliveryTerms;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\DespatchDocumentReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\InvoiceLine;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\LegalMonetaryTotal;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\OrderReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\OriginatorDocumentReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PayeeParty;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PaymentAlternativeExchangeRate;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PaymentExchangeRate;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PaymentMeans;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PaymentTerms;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Period;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PrepaidPayment;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PricingExchangeRate;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\ProjectReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\ReceiptDocumentReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\SellerSupplierParty;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Signature;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\StatementDocumentReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\TaxExchangeRate;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\TaxRepresentativeParty;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\TaxTotal;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\WithholdingTaxTotal;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Code;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Numeric;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Text;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlNamespace;
use JMS\Serializer\Annotation\XmlRoot;

/**
 * Class Invoice
 *
 * https://docs.peppol.eu/poacc/billing/3.0/syntax/ubl-invoice/
 */
#[XmlRoot(name: "Invoice", namespace: "urn:oasis:names:specification:ubl:schema:xsd:Invoice-2")]
#[XmlNamespace(uri: "urn:oasis:names:specification:ubl:schema:xsd:Invoice-2", prefix: "")]
#[XmlNamespace(uri: Namespaces::CAC, prefix: "cac")]
#[XmlNamespace(uri: Namespaces::CBC, prefix: "cbc")]
class Invoice implements Document
{
    #[SerializedName(name: "UBLVersionID")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string $ublVersionId = null;

    #[SerializedName(name: "CustomizationID")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string $customizationId = null;

    #[SerializedName(name: "ProfileID")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string $profileId = null;

    #[SerializedName(name: "ID")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string $id = null;

    #[SerializedName(name: "CopyIndicator")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|bool $copyIndicator = null;

    #[SerializedName(name: "UUID")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string $uuid = null;

    #[SerializedName(name: "IssueDate")]
    #[Type(name: "DateTime<'Y-m-d'>")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|DateTime $issueDate = null;

    #[SerializedName(name: "IssueTime")]
    #[Type(name: "DateTime<'H:i:s'>")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|DateTime $issueTime = null;

    #[SerializedName(name: "DueDate")]
    #[Type(name: "DateTime<'Y-m-d'>")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|DateTime $dueDate = null;

    #[SerializedName(name: "InvoiceTypeCode")]
    #[Type(name: Code::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Code $invoiceTypeCode = null;

    #[Type(name: 'array<' . Text::class . '>')]
    #[XmlList(
        entry: "Note",
        inline: true,
        namespace: Namespaces::CBC
    )]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    /** @var array<Text> $note */
    public array $note = [];

    #[SerializedName(name: "TaxPointDate")]
    #[Type(name: "DateTime<'Y-m-d'>")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|DateTime $taxPointDate = null;

    #[SerializedName(name: "DocumentCurrencyCode")]
    #[Type(name: Code::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Code $documentCurrencyCode = null;

    #[SerializedName(name: "TaxCurrencyCode")]
    #[Type(name: Code::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Code $taxCurrencyCode = null;

    #[SerializedName(name: "PricingCurrencyCode")]
    #[Type(name: Code::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Code $pricingCurrencyCode = null;

    #[SerializedName(name: "PaymentCurrencyCode")]
    #[Type(name: Code::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Code $paymentCurrencyCode = null;

    #[SerializedName(name: "PaymentAlternativeCurrencyCode")]
    #[Type(name: Code::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Code $paymentAlternativeCurrencyCode = null;

    #[SerializedName(name: "AccountingCostCode")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Code $accountingCostCode = null;

    #[SerializedName(name: "AccountingCost")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Text $accountingCost = null;

    #[SerializedName(name: "LineCountNumeric")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Numeric $lineCountNumeric = null;

    #[SerializedName(name: "BuyerReference")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Text $buyerReference = null;

    #[SerializedName(name: "InvoicePeriod")]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|Period $invoicePeriod = null;

    #[SerializedName(name: "OrderReference")]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|OrderReference $orderReference = null;

    #[SerializedName(name: "BillingReference")]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|BillingReference $billingReference = null;

    #[Type(name: 'array<' . DespatchDocumentReference::class . '>')]
    #[XmlList(entry: "DespatchDocumentReference", inline: true, namespace: Namespaces::CAC)]
    /** @var array<DespatchDocumentReference> $despatchDocumentReference */
    public array $despatchDocumentReference = [];

    #[Type(name: 'array<' . ReceiptDocumentReference::class . '>')]
    #[XmlList(entry: "ReceiptDocumentReference", inline: true, namespace: Namespaces::CAC)]
    /** @var array<ReceiptDocumentReference> $receiptDocumentReference */
    public array $receiptDocumentReference = [];

    #[Type(name: 'array<' . StatementDocumentReference::class . '>')]
    #[XmlList(entry: "StatementDocumentReference", inline: true, namespace: Namespaces::CAC)]
    /** @var array<StatementDocumentReference> $statementDocumentReference */
    public array $statementDocumentReference = [];

    #[Type(name: 'array<' . OriginatorDocumentReference::class . '>')]
    #[XmlList(entry: "OriginatorDocumentReference", inline: true, namespace: Namespaces::CAC)]
    /** @var array<OriginatorDocumentReference> $originatorDocumentReference */
    public array $originatorDocumentReference = [];

    #[Type(name: 'array<' . ContractDocumentReference::class . '>')]
    #[XmlList(entry: "ContractDocumentReference", inline: true, namespace: Namespaces::CAC)]
    /** @var array<ContractDocumentReference> $contractDocumentReference */
    public array $contractDocumentReference = [];

    #[Type(name: 'array<' . AdditionalDocumentReference::class . '>')]
    #[XmlList(entry: "AdditionalDocumentReference", inline: true, namespace: Namespaces::CAC)]
    /** @var array<AdditionalDocumentReference> $additionalDocumentReferences */
    public array $additionalDocumentReference = [];

    #[Type(name: 'array<' . ProjectReference::class . '>')]
    #[XmlList(entry: "ProjectReference", inline: true, namespace: Namespaces::CAC)]
    /** @var array<ProjectReference> $projectReference */
    public array $projectReference = [];

    #[Type(name: 'array<' . Signature::class . '>')]
    #[XmlList(entry: "Signature", inline: true, namespace: Namespaces::CAC)]
    /** @var array<Signature> $signature */
    public array $signature = [];

    #[SerializedName(name: "AccountingSupplierParty")]
    #[Type(name: AccountingSupplierParty::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public AccountingSupplierParty $accountingSupplierParty;

    #[SerializedName(name: "AccountingCustomerParty")]
    #[Type(name: AccountingCustomerParty::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public AccountingCustomerParty $accountingCustomerParty;

    #[SerializedName(name: "PayeeParty")]
    #[Type(name: PayeeParty::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|PayeeParty $payeeParty = null;

    #[SerializedName(name: "BuyerCustomerParty")]
    #[Type(name: BuyerCustomerParty::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|BuyerCustomerParty $buyerCustomerParty = null;

    #[SerializedName(name: "SellerSupplierParty")]
    #[Type(name: SellerSupplierParty::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|SellerSupplierParty $sellerSupplierParty = null;

    #[SerializedName(name: "TaxRepresentativeParty")]
    #[Type(name: TaxRepresentativeParty::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|TaxRepresentativeParty $taxRepresentativeParty = null;

    #[Type(name: 'array<' . Delivery::class . '>')]
    #[XmlList(entry: "Delivery", inline: true, namespace: Namespaces::CAC)]
    /** @var array<Delivery> $delivery */
    public array $delivery = [];

    #[SerializedName(name: "DeliveryTerms")]
    #[Type(name: DeliveryTerms::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|DeliveryTerms $deliveryTerms = null;

    #[Type(name: 'array<' . PaymentMeans::class . '>')]
    #[XmlList(entry: "PaymentMeans", inline: true, namespace: Namespaces::CAC)]
    /** @var array<PaymentMeans> $paymentMeans */
    public array $paymentMeans = [];

    #[Type(name: 'array<' . PaymentTerms::class . '>')]
    #[XmlList(entry: "PaymentTerms", inline: true, namespace: Namespaces::CAC)]
    /** @var array<PaymentTerms> $paymentTerms */
    public array $paymentTerms = [];

    #[Type(name: 'array<' . PrepaidPayment::class . '>')]
    #[XmlList(entry: "PrepaidPayment", inline: true, namespace: Namespaces::CAC)]
    /** @var array<PrepaidPayment> $prepaidPayment */
    public array $prepaidPayment = [];

    #[Type(name: 'array<' . AllowanceCharge::class . '>')]
    #[XmlList(entry: "AllowanceCharge", inline: true, namespace: Namespaces::CAC)]
    /** @var array<AllowanceCharge> $allowanceCharge */
    public array $allowanceCharge = [];

    #[SerializedName(name: "TaxExchangeRate")]
    #[Type(name: TaxExchangeRate::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|TaxExchangeRate $taxExchangeRate = null;

    #[SerializedName(name: "PricingExchangeRate")]
    #[Type(name: PricingExchangeRate::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|PricingExchangeRate $pricingExchangeRate = null;

    #[SerializedName(name: "PaymentExchangeRate")]
    #[Type(name: PaymentExchangeRate::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|PaymentExchangeRate $paymentExchangeRate = null;

    #[SerializedName(name: "PaymentAlternativeExchangeRate")]
    #[Type(name: PaymentAlternativeExchangeRate::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|PaymentAlternativeExchangeRate $paymentAlternativeExchangeRate = null;

    #[Type(name: 'array<' . TaxTotal::class . '>')]
    #[XmlList(entry: "TaxTotal", inline: true, namespace: Namespaces::CAC)]
    /** @var array<TaxTotal> $taxTotal */
    public array $taxTotal = [];

    #[Type(name: 'array<' . WithholdingTaxTotal::class . '>')]
    #[XmlList(entry: "WithholdingTaxTotal", inline: true, namespace: Namespaces::CAC)]
    /** @var array<WithholdingTaxTotal> $withholdingTaxTotal */
    public array $withholdingTaxTotal = [];

    #[SerializedName(name: "LegalMonetaryTotal")]
    #[Type(name: LegalMonetaryTotal::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public LegalMonetaryTotal $legalMonetaryTotal;

    #[Type(name: 'array<' . InvoiceLine::class . '>')]
    #[XmlList(entry: "InvoiceLine", inline: true, namespace: Namespaces::CAC)]
    /** @var array<InvoiceLine> $invoiceLine */
    public array $invoiceLine = [];

    public function findLines(): array
    {
        return $this->invoiceLine;
    }
}
