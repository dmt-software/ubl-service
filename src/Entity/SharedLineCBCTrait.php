<?php

namespace DMT\Ubl\Service\Entity;

use DMT\Ubl\Service\Entity\CommonBasicComponents\LineExtensionAmount;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;

trait SharedLineCBCTrait
{
    #[SerializedName(name: "ID")]
    #[Type(name: "string")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $id = null;

    #[SerializedName(name: "LineExtensionAmount")]
    #[Type(name: LineExtensionAmount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|LineExtensionAmount $lineExtensionAmount = null;
}
