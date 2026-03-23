<?php

namespace DMT\Ubl\Service\Entity\CommonAggregateComponents;

use DMT\Ubl\Service\Entity\CommonBasicComponents\Amount;
use DMT\Ubl\Service\Entity\Namespaces;
use DMT\Ubl\Service\Entity\Versions;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "TaxTotal",
    namespace: Namespaces::CAC
)]
class TaxTotal implements CommonAggregateComponent
{
    #[SerializedName(name: "TaxAmount")]
    #[Type(name: Amount::class)]
    #[XmlElement(cdata: false, namespace: Namespaces::CBC)]
    public null|float|Amount $taxAmount = null;

    #[Type(name: 'array<' . TaxSubtotal::class . '>')]
    #[Since(version: Versions::VERSION_2_0)]
    #[XmlList(
        entry: "TaxSubtotal",
        inline: true,
        namespace: Namespaces::CAC
    )]
    /** @var array<TaxSubtotal> $invoiceLine */
    public null|array $taxSubtotal = null;
}
