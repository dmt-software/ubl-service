<?php

namespace DMT\Ubl\Service\Entity\CommonAggregateComponents;

use DMT\Ubl\Service\Entity\CommonBasicComponents\Amount;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\SkipWhenEmpty;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "LegalMonetaryTotal",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class LegalMonetaryTotal implements CommonAggregateComponent
{
    #[SerializedName(name: "LineExtensionAmount")]
    #[Type(name: Amount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|Amount $lineExtensionAmount = null;

    #[SerializedName(name: "TaxExclusiveAmount")]
    #[Type(name: Amount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|Amount $taxExclusiveAmount = null;

    #[SerializedName(name: "TaxInclusiveAmount")]
    #[Type(name: Amount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|Amount $taxInclusiveAmount = null;

    #[SerializedName(name: "AllowanceTotalAmount")]
    #[SkipWhenEmpty]
    #[Type(name: Amount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|Amount $allowanceTotalAmount = null;

    #[SerializedName(name: "ChargeTotalAmount")]
    #[SkipWhenEmpty]
    #[Type(name: Amount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|Amount $chargeTotalAmount = null;

    #[SerializedName(name: "PrepaidAmount")]
    #[SkipWhenEmpty]
    #[Type(name: Amount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|Amount $prepaidAmount = null;

    #[SerializedName(name: "PayableRoundingAmount")]
    #[SkipWhenEmpty]
    #[Type(name: Amount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|Amount $payableRoundingAmount = null;

    #[SerializedName(name: "PayableAmount")]
    #[Type(name: Amount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|Amount $payableAmount = null;
}
