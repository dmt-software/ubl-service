<?php

namespace DMT\Ubl\Service\Entity\CommonAggregateComponents;

use DMT\Ubl\Service\Entity\Namespaces;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "AccountingCustomerParty",
    namespace: Namespaces::CAC
)]
class AccountingCustomerParty implements CommonAggregateComponent
{
    #[SerializedName("Party")]
    #[Type(Party::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CAC)]
    public null|Party $party = null;
}
