<?php

namespace DMT\Ubl\Service\Entity\Invoice;

use DateTime;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: 'InvoicePeriod',
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class InvoicePeriod
{
    #[SerializedName(name: "StartDate")]
    #[Type("DateTime<'Y-m-d'>")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|DateTime $startDate = null;

    #[SerializedName(name: "EndDate")]
    #[Type("DateTime<'Y-m-d'>")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|DateTime $endDate = null;
}
