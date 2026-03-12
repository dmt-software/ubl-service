<?php

namespace DMT\Ubl\Service\Entity;

use DateTime;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\InvoiceLine;
use DMT\Ubl\Service\Entity\CommonBasicComponents\InvoiceTypeCode;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlNamespace;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(name: "Invoice", namespace: "urn:oasis:names:specification:ubl:schema:xsd:Invoice-2")]
#[XmlNamespace(uri: "urn:oasis:names:specification:ubl:schema:xsd:Invoice-2", prefix: "")]
#[XmlNamespace(uri: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2", prefix: "cac")]
#[XmlNamespace(uri: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2", prefix: "cbc")]
class Invoice implements Document
{
    use SharedCBCTrait;
    use SharedCACTrait;

    #[SerializedName(name: "DueDate")]
    #[Type(name: "DateTime<'Y-m-d'>")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|DateTime $dueDate = null;

    #[SerializedName(name: "InvoiceTypeCode")]
    #[Type(name: InvoiceTypeCode::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|InvoiceTypeCode $invoiceTypeCode = null;

    #[Type(name: 'array<' . InvoiceLine::class . '>')]
    #[XmlList(
        entry: "InvoiceLine",
        inline: true,
        namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
    )]
    /** @var array<InvoiceLine> $invoiceLine */
    public null|array $invoiceLine = null;
}
