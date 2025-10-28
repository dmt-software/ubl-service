<?php

namespace DMT\Ubl\Service\Entity\Invoice\Type;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\SkipWhenEmpty;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlValue;
use Stringable;

#[XmlRoot(
    name: "PaymentMeansCode",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
)]
class PaymentMeansCode implements Stringable
{
    #[SkipWhenEmpty]
    #[XmlValue(cdata: false)]
    public null|string $code = null;

    #[SerializedName(name: "name")]
    #[XmlAttribute]
    public null|string $name = null;

    public function __toString(): string
    {
        return $this->code ?? '';
    }
}