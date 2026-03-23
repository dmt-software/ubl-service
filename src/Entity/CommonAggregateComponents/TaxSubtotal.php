<?php

namespace DMT\Ubl\Service\Entity\CommonAggregateComponents;

use DMT\Ubl\Service\Entity\CommonBasicComponents\Amount;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "TaxScheme",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class TaxSubtotal implements CommonAggregateComponent
{
    #[SerializedName(name: "TaxableAmount")]
    #[Type(name: Amount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|Amount $taxableAmount = null;

    #[SerializedName(name: "TaxAmount")]
    #[Type(name: Amount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|Amount $taxAmount = null;

    #[SerializedName(name: "TaxCategory")]
    #[Type(name: TaxCategory::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|TaxCategory $taxCategory = null;
}
