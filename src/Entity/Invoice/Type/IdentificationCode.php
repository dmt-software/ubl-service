<?php

namespace DMT\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Until;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlValue;
use Stringable;

#[XmlRoot(
    name: "IdentificationCode",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
)]
class IdentificationCode implements Stringable
{
    #[XmlValue(cdata: false)]
    public null|string $code = null;

    #[SerializedName(name: "listID")]
    #[Until(version: Invoice::VERSION_1_2)]
    #[XmlAttribute]
    public null|string $listId = 'ISO3166-1:Alpha2';

    #[SerializedName(name: "listAgencyID")]
    #[Until(version: Invoice::VERSION_1_2)]
    #[XmlAttribute]
    public null|string $listAgencyId = '6';

    public function __toString(): string
    {
        return $this->code ?? '';
    }
}
