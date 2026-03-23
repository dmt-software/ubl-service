<?php

namespace DMT\Ubl\Service\Entity\CommonAggregateComponents;

use DMT\Ubl\Service\Entity\Namespaces;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "AccountingSupplierParty",
    namespace: Namespaces::CAC
)]
class AccountingSupplierParty implements CommonAggregateComponent
{
    #[SerializedName("Party")]
    #[Type(Party::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|Party $party = null;
}
