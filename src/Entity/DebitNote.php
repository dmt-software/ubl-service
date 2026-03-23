<?php

namespace DMT\Ubl\Service\Entity;

use DateTime;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\AllowanceCharge;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\BillingReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\CustomerParty;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\DebitNoteLine;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Delivery;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\DeliveryTerms;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\DocumentReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\ExchangeRate;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\MonetaryTotal;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\OrderReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Party;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Payment;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PaymentMeans;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PaymentTerms;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Period;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Response;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Signature;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\SupplierParty;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\TaxTotal;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Code;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Identifier;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Indicator;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Numeric;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Text;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlNamespace;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(name: "DebitNote", namespace: "urn:oasis:names:specification:ubl:schema:xsd:Invoice-2")]
#[XmlNamespace(uri: "urn:oasis:names:specification:ubl:schema:xsd:Invoice-2", prefix: "")]
#[XmlNamespace(uri: Namespaces::CAC, prefix: "cac")]
#[XmlNamespace(uri: Namespaces::CBC, prefix: "cbc")]
class DebitNote implements Document
{
    #[SerializedName(name: "UBLVersionID")]
    #[Type(name: Identifier::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string|Identifier $ublVersionId = null;

    #[SerializedName(name: "CustomizationID")]
    #[Type(name: Identifier::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string|Identifier $customizationId = null;

    #[SerializedName(name: "ProfileID")]
    #[Type(name: Identifier::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string|Identifier $profileId = null;

    #[SerializedName(name: "ID")]
    #[Type(name: Identifier::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public string|Identifier $id;

    #[SerializedName(name: "CopyIndicator")]
    #[Type(name: Indicator::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|bool|Indicator $copyIndicator = null;

    #[SerializedName(name: "UUID")]
    #[Type(name: Identifier::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string|Identifier $uuid = null;

    #[SerializedName(name: "IssueDate")]
    #[Type(name: "DateTime<'Y-m-d'>")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public DateTime $issueDate;

    #[SerializedName(name: "IssueTime")]
    #[Type(name: "DateTime<'H:i:s'>")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|DateTime $issueTime = null;

    #[Type(name: 'array<' . Text::class . '>')]
    #[XmlList(entry: "Note", inline: true, namespace: Namespaces::CBC)]
    /** @var array<string|Text> $note */
    public array $note = [];

    #[SerializedName(name: "TaxPointDate")]
    #[Type(name: "DateTime<'Y-m-d'>")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|DateTime $taxPointDate = null;

    #[SerializedName(name: "DocumentCurrencyCode")]
    #[Type(name: Code::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string|Code $documentCurrencyCode = null;

    #[SerializedName(name: "TaxCurrencyCode")]
    #[Type(name: Code::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string|Code $taxCurrencyCode = null;

    #[SerializedName(name: "PricingCurrencyCode")]
    #[Type(name: Code::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string|Code $pricingCurrencyCode = null;

    #[SerializedName(name: "PaymentCurrencyCode")]
    #[Type(name: Code::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string|Code $paymentCurrencyCode = null;

    #[SerializedName(name: "PaymentAlternativeCurrencyCode")]
    #[Type(name: Code::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string|Code $paymentAlternativeCurrencyCode = null;

    #[SerializedName(name: "AccountingCostCode")]
    #[Type(name: Code::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string|Code $accountingCostCode = null;

    #[SerializedName(name: "AccountingCost")]
    #[Type(name: Text::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string|Text $accountingCost = null;

    #[SerializedName(name: "LineCountNumeric")]
    #[Type(name: Numeric::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Numeric $lineCountNumeric = null;

    #[Type(name: 'array<' . Period::class . '>')]
    #[XmlList(entry: "InvoicePeriod", inline: true, namespace: Namespaces::CAC)]
    /** @var array<Period> $invoicePeriod */
    public array $invoicePeriod = [];

    #[Type(name: 'array<' . Response::class . '>')]
    #[XmlList(entry: "DiscrepancyResponse", inline: true, namespace: Namespaces::CAC)]
    /** @var array<Response> $discrepancyResponse */
    public array $discrepancyResponse = [];

    #[SerializedName(name: "OrderReference")]
    #[Type(name: OrderReference::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|OrderReference $orderReference = null;

    #[Type(name: 'array<' . BillingReference::class . '>')]
    #[XmlList(entry: "BillingReference", inline: true, namespace: Namespaces::CAC)]
    /** @var array<BillingReference> $billingReference */
    public array $billingReference = [];

    #[Type(name: 'array<' . DocumentReference::class . '>')]
    #[XmlList(entry: "DespatchDocumentReference", inline: true, namespace: Namespaces::CAC)]
    /** @var array<DocumentReference> $despatchDocumentReference */
    public array $despatchDocumentReference = [];

    #[Type(name: 'array<' . DocumentReference::class . '>')]
    #[XmlList(entry: "ReceiptDocumentReference", inline: true, namespace: Namespaces::CAC)]
    /** @var array<DocumentReference> $receiptDocumentReference */
    public array $receiptDocumentReference = [];

    #[Type(name: 'array<' . DocumentReference::class . '>')]
    #[XmlList(entry: "StatementDocumentReference", inline: true, namespace: Namespaces::CAC)]
    /** @var array<DocumentReference> $statementDocumentReference */
    public array $statementDocumentReference = [];

    #[Type(name: 'array<' . DocumentReference::class . '>')]
    #[XmlList(entry: "ContractDocumentReference", inline: true, namespace: Namespaces::CAC)]
    /** @var array<DocumentReference> $contractDocumentReference */
    public array $contractDocumentReference = [];

    #[Type(name: 'array<' . DocumentReference::class . '>')]
    #[XmlList(entry: "AdditionalDocumentReference", inline: true, namespace: Namespaces::CAC)]
    /** @var array<DocumentReference> $additionalDocumentReference */
    public array $additionalDocumentReference = [];

    #[Type(name: 'array<' . Signature::class . '>')]
    #[XmlList(entry: "Signature", inline: true, namespace: Namespaces::CAC)]
    /** @var array<Signature> $signature */
    public array $signature = [];

    #[SerializedName(name: "AccountingSupplierParty")]
    #[Type(name: SupplierParty::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public SupplierParty $accountingSupplierParty;

    #[SerializedName(name: "AccountingCustomerParty")]
    #[Type(name: CustomerParty::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public CustomerParty $accountingCustomerParty;

    #[SerializedName(name: "PayeeParty")]
    #[Type(name: Party::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|Party $payeeParty = null;

    #[SerializedName(name: "BuyerCustomerParty")]
    #[Type(name: CustomerParty::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|CustomerParty $buyerCustomerParty = null;

    #[SerializedName(name: "SellerSupplierParty")]
    #[Type(name: SupplierParty::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|SupplierParty $sellerSupplierParty = null;

    #[SerializedName(name: "TaxRepresentativeParty")]
    #[Type(name: Party::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|Party $taxRepresentativeParty = null;

    #[Type(name: 'array<' . Payment::class . '>')]
    #[XmlList(entry: "PrepaidPayment", inline: true, namespace: Namespaces::CAC)]
    /** @var array<Payment> $prepaidPayment */
    public array $prepaidPayment = [];

    #[Type(name: 'array<' . AllowanceCharge::class . '>')]
    #[XmlList(entry: "AllowanceCharge", inline: true, namespace: Namespaces::CAC)]
    /** @var array<AllowanceCharge> $allowanceCharge */
    public array $allowanceCharge = [];

    #[Type(name: 'array<' . Delivery::class . '>')]
    #[XmlList(entry: "Delivery", inline: true, namespace: Namespaces::CAC)]
    /** @var array<Delivery> $delivery */
    public array $delivery = [];

    #[Type(name: 'array<' . DeliveryTerms::class . '>')]
    #[XmlList(entry: "DeliveryTerms", inline: true, namespace: Namespaces::CAC)]
    /** @var array<DeliveryTerms> $deliveryTerms */
    public array $deliveryTerms = [];

    #[Type(name: 'array<' . PaymentMeans::class . '>')]
    #[XmlList(entry: "PaymentMeans", inline: true, namespace: Namespaces::CAC)]
    /** @var array<PaymentMeans> $paymentMeans */
    public array $paymentMeans = [];

    #[Type(name: 'array<' . PaymentTerms::class . '>')]
    #[XmlList(entry: "PaymentTerms", inline: true, namespace: Namespaces::CAC)]
    /** @var array<PaymentTerms> $paymentTerms */
    public array $paymentTerms = [];

    #[SerializedName(name: "TaxExchangeRate")]
    #[Type(name: ExchangeRate::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|ExchangeRate $taxExchangeRate = null;

    #[SerializedName(name: "PricingExchangeRate")]
    #[Type(name: ExchangeRate::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|ExchangeRate $pricingExchangeRate = null;

    #[SerializedName(name: "PaymentExchangeRate")]
    #[Type(name: ExchangeRate::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|ExchangeRate $paymentExchangeRate = null;

    #[SerializedName(name: "PaymentAlternativeExchangeRate")]
    #[Type(name: ExchangeRate::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|ExchangeRate $paymentAlternativeExchangeRate = null;

    #[Type(name: 'array<' . TaxTotal::class . '>')]
    #[XmlList(entry: "TaxTotal", inline: true, namespace: Namespaces::CAC)]
    /** @var array<TaxTotal> $taxTotal */
    public array $taxTotal = [];

    #[SerializedName(name: "RequestedMonetaryTotal")]
    #[Type(name: MonetaryTotal::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public MonetaryTotal $requestedMonetaryTotal;

    #[Type(name: 'array<' . DebitNoteLine::class . '>')]
    #[XmlList(entry: "DebitNoteLine", inline: true, namespace: Namespaces::CAC)]
    /** @var array<DebitNoteLine> $debitNoteLine */
    public array $debitNoteLine = [];

    public function findLines(): array
    {
        return $this->debitNoteLine;
    }
}
