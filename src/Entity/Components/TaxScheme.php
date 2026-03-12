<?php

namespace DMT\Ubl\Service\Entity\Components;

use DMT\Ubl\Service\Entity\Components\Type\Id;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "TaxScheme",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class TaxScheme
{
    public const string STANDARD_TAX_RATE = 'S';
    public const string EXEMPT_FROM_TAX = 'E';
    public const string EXPORT_TAX_FREE = 'G';

    #[SerializedName(name: "ID")]
    #[Type(name: Id::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|Id $id = null;
}
