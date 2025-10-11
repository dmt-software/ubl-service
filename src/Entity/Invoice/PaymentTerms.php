<?php

namespace DMT\Ubl\Service\Entity\Invoice;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "PaymentTerms",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class PaymentTerms
{
    #[SerializedName(name: 'Note')]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $note = null;
}
