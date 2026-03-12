<?php

namespace DMT\Ubl\Service\Entity;

use DMT\Ubl\Service\Entity\Components\Type\InvoicedQuantity;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "InvoiceLine",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class InvoiceLine implements Entity
{
    use SharedLineCBCTrait;
    use SharedLineCACTrait;

    #[SerializedName(name: "InvoicedQuantity")]
    #[Type(name: InvoicedQuantity::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|int|InvoicedQuantity $invoicedQuantity = null;
}
