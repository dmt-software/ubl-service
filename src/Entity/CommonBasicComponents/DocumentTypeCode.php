<?php

namespace DMT\Ubl\Service\Entity\CommonBasicComponents;

use DMT\Ubl\Service\Entity\Versions;
use DMT\Ubl\Service\List\DocumentType;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Until;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlValue;
use Stringable;

#[XmlRoot(
    name: "DocumentTypeCode",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
)]
class DocumentTypeCode implements CommonBasicComponent, Stringable
{
    #[XmlValue(cdata: false)]
    #[Type(name: 'enum<' . DocumentType::class . '>')]
    public null|DocumentType $code = DocumentType::InvoiceObjectReference;

    #[SerializedName(name: "listID")]
    #[Until(version: Versions::VERSION_1_2)]
    #[XmlAttribute]
    public null|string $listId = 'UNCL1001';

    #[SerializedName(name: "listAgencyID")]
    #[Until(version: Versions::VERSION_1_2)]
    #[XmlAttribute]
    public null|string $listAgencyId = '6';

    public function __toString(): string
    {
        return $this->code->value ?? '';
    }
}
