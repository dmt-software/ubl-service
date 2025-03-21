<?php

namespace DMT\Ubl\Service\Entity\Invoice\Type;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlValue;
use Stringable;


#[XmlRoot(
    name: "BaseQuantity",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
)]
class BaseQuantity implements Stringable, QuantityType
{
    #[Type(name: "int")]
    #[XmlValue]
    public null|int $quantity = null;

    #[SerializedName(name: "unitCode")]
    #[XmlAttribute]
    public null|string $unitCode = null;

    public function __toString(): string
    {
        return strval($this->quantity);
    }

    public function setUnit(string $unit): void
    {
        $this->unitCode = $unit;
    }
}
