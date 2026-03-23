<?php

namespace DMT\Ubl\Service\Entity\CommonAggregateComponents;

use DMT\Ubl\Service\Entity\CommonBasicComponents\EmbeddedDocumentBinaryObject;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "Attachment",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class Attachment implements CommonAggregateComponent
{
    #[SerializedName(name: "EmbeddedDocumentBinaryObject")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|EmbeddedDocumentBinaryObject $embeddedDocumentBinaryObject = null;


    #[SerializedName(name: "ExternalReference")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|ExternalReference $externalReference = null;
}
