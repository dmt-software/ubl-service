<?php

namespace DMT\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\TaxableAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\TaxAmount;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;

class TaxSubtotal
{
    #[SerializedName(name: "TaxableAmount")]
    #[Type(name: TaxableAmount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|TaxableAmount $taxableAmount = null;

    #[SerializedName(name: "TaxAmount")]
    #[Type(name: TaxAmount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|TaxAmount $taxAmount = null;

    #[SerializedName(name: "TaxCategory")]
    #[Type(name: TaxCategory::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|TaxCategory $taxCategory = null;
}
