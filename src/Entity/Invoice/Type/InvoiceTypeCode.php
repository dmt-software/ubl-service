<?php

namespace DMT\Ubl\Service\Entity\Invoice\Type;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Until;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlValue;
use Stringable;

#[XmlRoot(
    name: "InvoiceTypeCode",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
)]
class InvoiceTypeCode implements Stringable
{
    #[XmlValue(cdata: false)]
    public null|string $code = null;

    #[SerializedName(name: "listID")]
    #[Until(version: "1.2")]
    #[XmlAttribute]
    public null|string $listId = 'UNCL1001';

    #[SerializedName(name: "listAgencyID")]
    #[Until(version: "1.2")]
    #[XmlAttribute]
    public null|string $listAgencyId = '6';

    public function __toString(): string
    {
        return $this->code ?? '';
    }
}
