<?php

namespace DMT\Ubl\Service\Entity\Invoice\Type;
use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\List\ElectronicAddressScheme;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Until;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlValue;
use Stringable;

#[XmlRoot(
    name: "CompanyID",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
)]
class CompanyId implements ElectronicAddressType, Stringable
{
    #[XmlValue(cdata: false)]
    public null|string $id = null;

    #[SerializedName(name: "schemeID")]
    #[XmlAttribute]
    public null|string|ElectronicAddressScheme $schemeId = null;

    #[SerializedName(name: "schemeAgencyID")]
    #[Until(version: Invoice::VERSION_1_2)]
    #[XmlAttribute]
    public null|string $schemeAgencyId = null;

    public function __toString(): string
    {
        return $this->id ?? '';
    }
}
