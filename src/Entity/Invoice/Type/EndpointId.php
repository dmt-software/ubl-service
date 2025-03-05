<?php

namespace DMT\Ubl\Service\Entity\Invoice\Type;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Until;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlValue;
use Stringable;

#[XmlRoot(
    name: "EndpointID",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
)]
class EndpointId implements Stringable
{
    #[XmlValue(cdata: false)]
    public null|string $id = null;

    #[SerializedName(name: "schemeID")]
    #[Since(version: "1.1")]
    #[XmlAttribute]
    public null|string $schemeId = null;

    #[SerializedName(name: "schemeAgencyID")]
    #[Since(version: "1.1")]
    #[Until(version: "1.2")]
    #[XmlAttribute]
    public null|string $schemeAgencyId = null;

    public function __toString(): string
    {
        return $this->id ?? '';
    }
}
