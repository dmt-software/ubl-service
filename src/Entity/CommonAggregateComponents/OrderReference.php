<?php

namespace DMT\Ubl\Service\Entity\CommonAggregateComponents;

use DMT\Ubl\Service\Entity\Namespaces;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "OrderReference",
    namespace: Namespaces::CAC
)]
class OrderReference implements CommonAggregateComponent
{
    #[SerializedName(name: "ID")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string $id = null;

    #[SerializedName(name: "SalesOrderID")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|string $salesOrderId = null;
}
