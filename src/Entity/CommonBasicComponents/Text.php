<?php

namespace DMT\Ubl\Service\Entity\CommonBasicComponents;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlValue;
use Stringable;

#[XmlRoot(
    name: "Note",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
)]
class Text implements Stringable
{
    #[Type(name: "string")]
    #[XmlValue]
    public null|string $text = null;

    #[SerializedName(name: "languageID")]
    #[XmlAttribute]
    public null|string $languageId = null;

    #[SerializedName(name: "languageLocaleID")]
    #[XmlAttribute]
    public null|string $languageLocaleId = null;

    public function __toString(): string
    {
        if (null === $this->text) {
            return '';
        }

        return $this->text;
    }
}
