<?php

namespace DMT\Ubl\Service\Entity;

use DateTime;
use DMT\Ubl\Service\Entity\CommonBasicComponents\CurrencyCode;
use DMT\Ubl\Service\Entity\CommonBasicComponents\TaxCurrencyCode;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Until;
use JMS\Serializer\Annotation\XmlElement;

trait SharedCBCTrait
{
    #[SerializedName(name: "ID")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $id = null;

    #[SerializedName(name: "IssueDate")]
    #[Type(name: "DateTime<'Y-m-d'>")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|DateTime $issueDate = null;

    #[SerializedName(name: "IssueTime")]
    #[Type(name: "DateTime<'H:i:s'>")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|DateTime $issueTime = null;

    #[SerializedName(name: "TaxPointDate")]
    #[Since(version: Versions::VERSION_1_1)]
    #[Type(name: "DateTime<'Y-m-d'>")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|DateTime $taxPointDate = null;

    #[SerializedName(name: "Note")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $note = null;

    #[SerializedName(name: "DocumentCurrencyCode")]
    #[Type(name: CurrencyCode::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|CurrencyCode $documentCurrencyCode = null;

    #[SerializedName(name: "TaxCurrencyCode")]
    #[Type(name: TaxCurrencyCode::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|TaxCurrencyCode $taxCurrencyCode = null;

    #[SerializedName(name: "AccountingCost")]
    #[Since(version: Versions::VERSION_1_1)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $accountingCost = null;

    // accountingCostCode

    #[SerializedName(name: "CopyIndicator")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|bool $copyIndicator = null;

    #[SerializedName(name: "UUID")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $uuid = null;

    #[SerializedName(name: "BuyerReference")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $buyerReference = null;

    #[SerializedName(name: "PricingCurrencyCode")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $pricingCurrencyCode = null;

    #[SerializedName(name: "PaymentCurrencyCode")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $paymentCurrencyCode = null;

    #[SerializedName(name: "PaymentAlternativeCurrencyCode")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $paymentAlternativeCurrencyCode = null;

    #[SerializedName(name: "AccountingCostCode")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $accountingCostCode = null;

    #[SerializedName(name: "LineCountNumeric")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|int $lineCountNumeric = null;
}
