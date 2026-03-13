<?php

namespace DMT\Ubl\Service\Entity\CommonBasicComponents;

use DMT\Ubl\Service\Entity\CommonAggregateComponents\CommonAggregateComponent;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlValue;

#[XmlRoot(
    name: "Amount",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
)]
class EmbeddedDocumentBinaryObject implements CommonAggregateComponent
{
    #[XmlValue]
    public null|string $contents = null;

    #[SerializedName(name: "mimeCode")]
    #[XmlAttribute]
    public null|string $mimeCode = null;

    #[SerializedName(name: "filename")]
    #[XmlAttribute]
    public null|string $filename = null;

    public function __toString(): string
    {
        if (null === $this->contents) {
            return '';
        }

        return base64_decode($this->contents);
    }
}
