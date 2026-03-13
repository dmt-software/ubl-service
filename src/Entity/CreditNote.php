<?php

namespace DMT\Ubl\Service\Entity;

use DMT\Ubl\Service\Entity\CommonAggregateComponents\CreditNoteLine;
use DMT\Ubl\Service\Entity\CommonBasicComponents\CreditNoteTypeCode;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlNamespace;
use JMS\Serializer\Annotation\XmlRoot;

/**
 * Class CreditNote
 *
 * https://docs.peppol.eu/poacc/billing/3.0/syntax/ubl-creditnote/tree/
 */
#[XmlRoot(name: "CreditNote", namespace: "urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2")]
#[XmlNamespace(uri: "urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2", prefix: "")]
#[XmlNamespace(uri: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2", prefix: "cac")]
#[XmlNamespace(uri: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2", prefix: "cbc")]
class CreditNote implements Document
{
    use SharedCBCTrait;
    use SharedCACTrait;

    #[SerializedName(name: "CreditNoteTypeCode")]
    #[Type(name: CreditNoteTypeCode::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|CreditNoteTypeCode $creditNoteTypeCode = null;

    #[Type(name: 'array<' . CreditNoteLine::class . '>')]
    #[XmlList(
        entry: "CreditNoteLine",
        inline: true,
        namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
    )]
    /** @var array<CreditNoteLine> $creditNoteLine */
    public null|array $creditNoteLine = null;
}
