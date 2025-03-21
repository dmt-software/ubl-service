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
    #[Type(name: "float")]
    #[XmlValue]
    public null|float $amount = null;

    #[SerializedName(name: "currencyID")]
    #[XmlAttribute]
    public null|string $currencyId = null;

    public function __toString(): string
    {
        if (null === $this->amount) {
            return '';
        }

        $digits = max(strlen(strstr(rtrim(strval($this->amount) ,'0'), '.')) - 1, 2);

        return sprintf("%.$digits" . "f %s", $this->amount, $this->currencyId);
    }

    public function setCurrency(?string $currency): void
    {
        $this->currencyId = $currency;
    }
}
