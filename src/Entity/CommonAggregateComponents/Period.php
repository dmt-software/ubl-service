<?php

namespace DMT\Ubl\Service\Entity\CommonAggregateComponents;

use DateTime;
use DMT\Ubl\Service\Entity\Namespaces;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: 'Period',
    namespace: Namespaces::CAC
)]
class Period implements CommonAggregateComponent
{
    #[SerializedName(name: "StartDate")]
    #[Type("DateTime<'Y-m-d'>")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|DateTime $startDate = null;

    #[SerializedName(name: "EndDate")]
    #[Type("DateTime<'Y-m-d'>")]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|DateTime $endDate = null;
}
