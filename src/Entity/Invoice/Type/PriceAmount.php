<?php

namespace DMT\Ubl\Service\Entity\Invoice\Type;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlValue;
use Stringable;

#[XmlRoot(
    name: "PriceAmount",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
)]
class PriceAmount implements Stringable, AmountType
{
    #[XmlValue]
    #[Type(name: "float")]
    public null|float $amount = null;

    #[SerializedName(name: "currencyId")]
    #[XmlAttribute]
    public null|string $currencyId = null;

    public function __toString(): string
    {
        if (null === $this->amount) {
            return '';
        }

        return sprintf("%f %s", $this->amount, $this->currencyId);
    }

    public function setCurrency(?string $currency): void
    {
        $this->currencyId = $currency;
    }
}
