<?php

namespace DMT\Ubl\Service\Entity\CommonBasicComponents;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlValue;

class Numeric
{
    #[XmlValue]
    #[Type(name: "float")]
    public null|float $value = null;

    #[XmlAttribute]
    public null|string $format = null;
}