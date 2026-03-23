<?php

namespace DMT\Ubl\Service\Entity\CommonAggregateComponents;

use DMT\Ubl\Service\Entity\CommonBasicComponents\Quantity;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "InvoiceLine",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class InvoiceLine implements CommonAggregateComponent
{
    use SharedLineCBCTrait;
    use SharedLineCACTrait;

    #[SerializedName(name: "InvoicedQuantity")]
    #[Type(name: Quantity::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|int|Quantity $invoicedQuantity = null;
}
