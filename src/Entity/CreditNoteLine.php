<?php

namespace DMT\Ubl\Service\Entity;

use DMT\Ubl\Service\Entity\Components\Type\CreditedQuantity;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "CreditNoteLine",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class CreditNoteLine implements Entity
{
    use SharedLineCBCTrait;
    use SharedLineCACTrait;

    #[SerializedName(name: "CreditedQuantity")]
    #[Type(name: CreditedQuantity::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|int|CreditedQuantity $creditedQuantity = null;
}
