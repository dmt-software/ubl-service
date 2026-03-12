<?php

namespace DMT\Ubl\Service\Entity\Components;

use DMT\Ubl\Service\Entity\Components\Type\TaxAmount;
use DMT\Ubl\Service\Entity\Versions;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "TaxTotal",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class TaxTotal
{
    #[SerializedName(name: "TaxAmount")]
    #[Type(name: TaxAmount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|float|TaxAmount $taxAmount = null;

    #[Type(name: 'array<' . TaxSubtotal::class . '>')]
    #[Since(version: Versions::VERSION_2_0)]
    #[XmlList(
        entry: "TaxSubtotal",
        inline: true,
        namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
    )]
    /** @var array<TaxSubtotal> $invoiceLine */
    public null|array $taxSubtotal = null;
}
