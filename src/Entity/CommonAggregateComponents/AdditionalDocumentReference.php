<?php

namespace DMT\Ubl\Service\Entity\CommonAggregateComponents;

use DMT\Ubl\Service\Entity\CommonBasicComponents\DocumentTypeCode;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "AdditionalDocumentReference",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class AdditionalDocumentReference implements CommonAggregateComponent
{
    #[SerializedName(name: "ID")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $id = null;

    #[SerializedName(name: "DocumentTypeCode")]
    #[Type(name: DocumentTypeCode::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|DocumentTypeCode $documentTypeCode = null;

    #[SerializedName(name: "DocumentDescription")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $documentDescription = null;

    #[SerializedName(name: "Attachment")]
    #[Type(name: Attachment::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|Attachment $attachment = null;
}
