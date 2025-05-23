<?php

namespace DMT\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\CompanyId;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "PartyLegalEntity",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class PartyLegalEntity
{
    #[SerializedName(name: "RegistrationName")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $registrationName = null;

    #[SerializedName(name: "CompanyID")]
    #[Type(name: CompanyId::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|CompanyId $companyId = null;
}
