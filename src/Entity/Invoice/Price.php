<?php

namespace DMT\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\BaseQuantity;
use DMT\Ubl\Service\Entity\Invoice\Type\PriceAmount;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "Price",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class Price
{
    #[SerializedName(name: "PriceAmount")]
    #[Type(name: PriceAmount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|PriceAmount $priceAmount = null;

    #[SerializedName(name: "BaseQuantity")]
    #[Type(name: BaseQuantity::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|BaseQuantity $baseQuantity = null;
}
