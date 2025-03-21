<?php

namespace DMT\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\AllowanceTotalAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\ChargeTotalAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\LineExtensionAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\PayableAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\PayableRoundingAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\PrepaidAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\TaxExclusiveAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\TaxInclusiveAmount;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\SkipWhenEmpty;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "LegalMonetaryTotal",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class LegalMonetaryTotal
{
    #[SerializedName(name: "LineExtensionAmount")]
    #[Type(name: LineExtensionAmount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|LineExtensionAmount $lineExtensionAmount = null;

    #[SerializedName(name: "TaxExclusiveAmount")]
    #[Type(name: TaxExclusiveAmount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|TaxExclusiveAmount $taxExclusiveAmount = null;

    #[SerializedName(name: "TaxInclusiveAmount")]
    #[Type(name: TaxInclusiveAmount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|TaxInclusiveAmount $taxInclusiveAmount = null;

    #[SerializedName(name: "AllowanceTotalAmount")]
    #[SkipWhenEmpty]
    #[Type(name: AllowanceTotalAmount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|AllowanceTotalAmount $allowanceTotalAmount = null;

    #[SerializedName(name: "ChargeTotalAmount")]
    #[SkipWhenEmpty]
    #[Type(name: ChargeTotalAmount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|ChargeTotalAmount $chargeTotalAmount = null;

    #[SerializedName(name: "PrepaidAmount")]
    #[SkipWhenEmpty]
    #[Type(name: PrepaidAmount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|PrepaidAmount $prepaidAmount = null;

    #[SerializedName(name: "PayableRoundingAmount")]
    #[SkipWhenEmpty]
    #[Type(name: PayableRoundingAmount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|PayableRoundingAmount $payableRoundingAmount = null;

    #[SerializedName(name: "PayableAmount")]
    #[Type(name: PayableAmount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|PayableAmount $payableAmount = null;
}
