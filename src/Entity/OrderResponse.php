<?php

namespace DMT\Ubl\Service\Entity;

use DateTime;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\AccountingCustomerParty;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\AccountingSupplierParty;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\AdditionalDocumentReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\AllowanceCharge;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\BillingReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\BuyerCustomerParty;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Contract;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Delivery;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\DeliveryTerms;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\DestinationCountry;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\FreightForwarderParty;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\LegalMonetaryTotal;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\OrderDocumentReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\OrderLine;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\OrderReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\OriginatorCustomerParty;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\OriginatorDocumentReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PaymentExchangeRate;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PaymentMeans;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PaymentTerms;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Period;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PricingExchangeRate;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\SellerSupplierParty;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Signature;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\TaxExchangeRate;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\TaxTotal;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\TransactionConditions;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Code;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Measure;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Numeric;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Quantity;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Text;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlNamespace;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(name: "OrderResponse", namespace: "urn:oasis:names:specification:ubl:schema:xsd:OrderResponse-2")]
#[XmlNamespace(uri: "urn:oasis:names:specification:ubl:schema:xsd:OrderResponse-2", prefix: "")]
#[XmlNamespace(uri: Namespaces::CAC, prefix: "cac")]
#[XmlNamespace(uri: Namespaces::CBC, prefix: "cbc")]
class OrderResponse implements Document
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

    #[SerializedName(name: "SalesOrderID")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string $salesOrderId = null;

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

    #[SerializedName(name: "OrderResponseCode")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string $orderResponseCode = null;

    #[Type(name: 'array<' . Text::class . '>')]
    #[XmlList(
        entry: "Note",
        inline: true,
        namespace: Namespaces::CBC
    )]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    /** @var array<Text> $note */
    public array $note = [];

    #[SerializedName(name: "DocumentCurrencyCode")]
    #[Type(name: Code::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Code $documentCurrencyCode = null;

    #[SerializedName(name: "PricingCurrencyCode")]
    #[Type(name: Code::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Code $pricingCurrencyCode = null;

    #[SerializedName(name: "TaxCurrencyCode")]
    #[Type(name: Code::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Code $taxCurrencyCode = null;

    #[SerializedName(name: "TotalPackagesQuantity")]
    #[Type(name: Quantity::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Quantity $totalPackagesQuantity = null;

    #[SerializedName(name: "GrossWeightMeasure")]
    #[Type(name: Measure::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Measure $grossWeightMeasure = null;

    #[SerializedName(name: "NetWeightMeasure")]
    #[Type(name: Measure::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Measure $netWeightMeasure = null;

    #[SerializedName(name: "NetNetWeightMeasure")]
    #[Type(name: Measure::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Measure $netNetWeightMeasure = null;

    #[SerializedName(name: "GrossVolumeMeasure")]
    #[Type(name: Measure::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Measure $grossVolumeMeasure = null;

    #[SerializedName(name: "NetVolumeMeasure")]
    #[Type(name: Measure::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Measure $netVolumeMeasure = null;

    #[SerializedName(name: "CustomerReference")]
    #[Type(name: Text::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Text $customerReference = null;

    #[SerializedName(name: "AccountingCostCode")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Code $accountingCostCode = null;

    #[SerializedName(name: "AccountingCost")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Text $accountingCost = null;

    #[SerializedName(name: "LineCountNumeric")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|Numeric $lineCountNumeric = null;

    #[SerializedName(name: "ValidityPeriod")]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|Period $validityPeriod = null;

    #[SerializedName(name: "OrderReference")]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|OrderReference $orderReference = null;

    #[Type(name: 'array<' . OrderDocumentReference::class . '>')]
    #[XmlList(entry: "OrderDocumentReference", inline: true, namespace: Namespaces::CAC)]
    /** @var array<OrderDocumentReference> $orderDocumentReference */
    public array $orderDocumentReference = [];

    #[SerializedName(name: "OriginatorDocumentReference")]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|OriginatorDocumentReference $originatorDocumentReference = null;

    #[Type(name: 'array<' . AdditionalDocumentReference::class . '>')]
    #[XmlList(entry: "AdditionalDocumentReference", inline: true, namespace: Namespaces::CAC)]
    /** @var array<AdditionalDocumentReference> $additionalDocumentReferences */
    public array $additionalDocumentReference = [];

    #[Type(name: 'array<' . Contract::class . '>')]
    #[XmlList(entry: "Contract", inline: true, namespace: Namespaces::CAC)]
    /** @var array<Contract> $contract */
    public array $contract = [];

    #[Type(name: 'array<' . Signature::class . '>')]
    #[XmlList(entry: "Signature", inline: true, namespace: Namespaces::CAC)]
    /** @var array<Signature> $signature */
    public array $signature = [];

    #[SerializedName(name: "SellerSupplierParty")]
    #[Type(name: SellerSupplierParty::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public SellerSupplierParty $sellerSupplierParty;

    #[SerializedName(name: "BuyerCustomerParty")]
    #[Type(name: BuyerCustomerParty::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public BuyerCustomerParty $buyerCustomerParty;

    #[SerializedName(name: "OriginatorCustomerParty")]
    #[Type(name: OriginatorCustomerParty::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|OriginatorCustomerParty $originatorCustomerParty = null;

    #[SerializedName(name: "FreightForwarderParty")]
    #[Type(name: FreightForwarderParty::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|FreightForwarderParty $freightForwarderParty = null;

    #[SerializedName(name: "AccountingSupplierParty")]
    #[Type(name: AccountingSupplierParty::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|AccountingSupplierParty $accountingSupplierParty = null;

    #[SerializedName(name: "AccountingCustomerParty")]
    #[Type(name: AccountingCustomerParty::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|AccountingCustomerParty $accountingCustomerParty = null;

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

    #[Type(name: 'array<' . AllowanceCharge::class . '>')]
    #[XmlList(entry: "AllowanceCharge", inline: true, namespace: Namespaces::CAC)]
    /** @var array<AllowanceCharge> $allowanceCharge */
    public array $allowanceCharge = [];

    #[SerializedName(name: "TransactionConditions")]
    #[Type(name: TransactionConditions::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|TransactionConditions $transactionConditions = null;

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

    #[SerializedName(name: "DestinationCountry")]
    #[Type(name: DestinationCountry::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|DestinationCountry $destinationCountry = null;

    #[Type(name: 'array<' . TaxTotal::class . '>')]
    #[XmlList(entry: "TaxTotal", inline: true, namespace: Namespaces::CAC)]
    /** @var array<TaxTotal> $taxTotal */
    public array $taxTotal = [];

    #[SerializedName(name: "LegalMonetaryTotal")]
    #[Type(name: LegalMonetaryTotal::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|LegalMonetaryTotal $legalMonetaryTotal = null;

    #[Type(name: 'array<' . OrderLine::class . '>')]
    #[XmlList(entry: "OrderLine", inline: true, namespace: Namespaces::CAC)]
    /** @var array<OrderLine> $orderLine */
    public array $orderLine = [];

    public function findLines(): array
    {
        return $this->orderLine ?? [];
    }
}