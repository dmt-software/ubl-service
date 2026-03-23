<?php

namespace DMT\Ubl\Service\Entity\CommonBasicComponents;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlValue;
use Stringable;

#[XmlRoot(
    name: "Note",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
)]
class Measure implements CommonBasicComponent, Stringable
{
    #[Type(name: "float")]
    #[XmlValue]
    public null|float $measure = null;

    #[Type(name: "string")]
    #[XmlAttribute]
    public null|string $unitCode = null;

    #[Type(name: "string")]
    #[XmlAttribute]
    public null|string $unitCodeListVersionID = null;

    public function __toString(): string
    {
        if (null === $this->measure) {
            return '';
        }

        return sprintf("%.2f %s", $this->measure, $this->unitCode);
    }
}