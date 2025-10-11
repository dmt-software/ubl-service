<?php

namespace DMT\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\Type\Id;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "ClassifiedTaxCategory",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class ClassifiedTaxCategory
{
    #[SerializedName(name: "ID")]
    #[Type(name: Id::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|Id $id = null;

    #[SerializedName(name: "Percent")]
    #[Type(name: "int")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|int $percent = null;

    #[SerializedName(name: "TaxScheme")]
    #[Type(name: TaxScheme::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|TaxScheme $taxScheme = null;
}
