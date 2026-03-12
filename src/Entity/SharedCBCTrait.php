<?php

namespace DMT\Ubl\Service\Entity;

use DateTime;
use DMT\Ubl\Service\Entity\Components\Type\DocumentCurrencyCode;
use DMT\Ubl\Service\Entity\Components\Type\TaxCurrencyCode;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Until;
use JMS\Serializer\Annotation\XmlElement;

trait SharedCBCTrait
{
    #[SerializedName(name: "UBLVersionID")]
    #[Until(version: Versions::VERSION_1_2)]
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

    #[SerializedName(name: "TaxPointDate")]
    #[Since(version: Versions::VERSION_1_1)]
    #[Type(name: "DateTime<'Y-m-d'>")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|DateTime $taxPointDate = null;

    #[SerializedName(name: "Note")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $note = null;

    #[SerializedName(name: "DocumentCurrencyCode")]
    #[Type(name: DocumentCurrencyCode::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|DocumentCurrencyCode $documentCurrencyCode = null;

    #[SerializedName(name: "TaxCurrencyCode")]
    #[Type(name: TaxCurrencyCode::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|TaxCurrencyCode $taxCurrencyCode = null;

    #[SerializedName(name: "AccountingCost")]
    #[Since(version: Versions::VERSION_1_1)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $accountingCost = null;

    #[SerializedName(name: "BuyerReference")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $buyerReference = null;
}
