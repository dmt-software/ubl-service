<?php

namespace DMT\Ubl\Service\Entity\Invoice;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "Delivery",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class Delivery
{
    #[SerializedName(name: "DeliveryLocation")]
    #[Type(name: DeliveryLocation::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|DeliveryLocation $deliveryLocation = null;
}
