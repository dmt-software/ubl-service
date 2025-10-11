<?php

namespace DMT\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\CompanyId;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "PartyTaxScheme",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class PartyTaxScheme
{
    #[SerializedName(name: "CompanyID")]
    #[Type(name: CompanyId::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|CompanyId $companyId = null;

    #[SerializedName(name: "TaxScheme")]
    #[Type(name: TaxScheme::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|TaxScheme $taxScheme = null;
}
