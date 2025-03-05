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
    #[XmlElement(cdata: false, namespace: "LineExtensionAmount")]
    public null|LineExtensionAmount $lineExtensionAmount = null;

    #[SerializedName(name: "TaxExclusiveAmount")]
    #[Type(name: TaxExclusiveAmount::class)]
    #[XmlElement(cdata: false, namespace: "TaxExclusiveAmount")]
    public null|TaxExclusiveAmount $taxExclusiveAmount = null;

    #[SerializedName(name: "TaxInclusiveAmount")]
    #[Type(name: TaxInclusiveAmount::class)]
    #[XmlElement(cdata: false, namespace: "TaxInclusiveAmount")]
    public null|TaxInclusiveAmount $taxInclusiveAmount = null;

    #[SerializedName(name: "AllowanceTotalAmount")]
    #[Type(name: AllowanceTotalAmount::class)]
    #[XmlElement(cdata: false, namespace: "AllowanceTotalAmount")]
    public null|AllowanceTotalAmount $allowanceTotalAmount = null;

    #[SerializedName(name: "ChargeTotalAmount")]
    #[Type(name: ChargeTotalAmount::class)]
    #[XmlElement(cdata: false, namespace: "ChargeTotalAmount")]
    public null|ChargeTotalAmount $chargeTotalAmount = null;

    #[SerializedName(name: "PrepaidAmount")]
    #[Type(name: PrepaidAmount::class)]
    #[XmlElement(cdata: false, namespace: "PrepaidAmount")]
    public null|PrepaidAmount $prepaidAmount = null;

    #[SerializedName(name: "PayableRoundingAmount")]
    #[Type(name: PayableRoundingAmount::class)]
    #[XmlElement(cdata: false, namespace: "PayableRoundingAmount")]
    public null|PayableRoundingAmount $payableRoundingAmount = null;

    #[SerializedName(name: "PayableAmount")]
    #[Type(name: PayableAmount::class)]
    #[XmlElement(cdata: false, namespace: "PayableAmount")]
    public null|PayableAmount $payableAmount = null;
}
