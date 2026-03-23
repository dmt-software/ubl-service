<?php

namespace DMT\Ubl\Service\Entity\CommonBasicComponents;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlValue;

class Code
{
    #[XmlValue]
    public null|string $code = null;

    #[SerializedName(name: "listID")]
    #[XmlAttribute]
    public null|string $listId = null;

    #[SerializedName(name: "listAgencyID")]
    #[XmlAttribute]
    public null|string $listAgencyId = null;

    #[XmlAttribute]
    public null|string $listAgencyName = null;

    #[XmlAttribute]
    public null|string $listName = null;

    #[SerializedName(name: "listVersionID")]
    #[XmlAttribute]
    public null|string $listVersionId = null;

    #[XmlAttribute]
    public null|string $name = null;

    #[SerializedName(name: "languageID")]
    #[XmlAttribute]
    public null|string $languageId = null;

    #[SerializedName(name: "listURI")]
    #[XmlAttribute]
    public null|string $listUri = null;

    #[SerializedName(name: "listSchemeURI")]
    #[XmlAttribute]
    public null|string $listSchemeUri = null;
}