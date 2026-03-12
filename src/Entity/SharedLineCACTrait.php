<?php

namespace DMT\Ubl\Service\Entity;

use DMT\Ubl\Service\Entity\CommonAggregateComponents\AllowanceCharge;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Item;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Price;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\TaxTotal;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Until;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlList;

trait SharedLineCACTrait
{
    #[Since(version: Versions::VERSION_2_0)]
    #[Type(name: 'array<' . AllowanceCharge::class . '>')]
    #[XmlList(
        entry: "AllowanceCharge",
        inline: true,
        namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
    )]
    /** @var array<AllowanceCharge> $allowanceCharge */
    public null|array $allowanceCharge = null;

    #[SerializedName(name: "TaxTotal")]
    #[Type(name: TaxTotal::class)]
    #[Until(version: Versions::VERSION_1_2)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|TaxTotal $taxTotal = null;

    #[SerializedName(name: "Item")]
    #[Type(name: Item::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|Item $item = null;

    #[SerializedName(name: "Price")]
    #[Type(name: Price::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|Price $price = null;
}
