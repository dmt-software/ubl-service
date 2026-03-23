<?php

namespace DMT\Ubl\Service\Entity\CommonAggregateComponents;

use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "BillingReference",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class BillingReference
{

}