<?php

namespace DMT\Ubl\Service\Entity;

use DMT\Ubl\Service\Entity\Invoice\AllowanceCharge;
use DMT\Ubl\Service\Entity\Invoice\Item;
use DMT\Ubl\Service\Entity\Invoice\Price;
use DMT\Ubl\Service\Entity\Invoice\TaxTotal;
use DMT\Ubl\Service\Entity\Invoice\Type\InvoicedQuantity;
use DMT\Ubl\Service\Entity\Invoice\Type\LineExtensionAmount;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Until;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "InvoiceLine",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class InvoiceLine
{
    #[SerializedName(name: "ID")]
    #[Type(name: "string")]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|string $id = null;

    #[SerializedName(name: "InvoicedQuantity")]
    #[Type(name: InvoicedQuantity::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|InvoicedQuantity $invoicedQuantity = null;

    #[SerializedName(name: "LineExtensionAmount")]
    #[Type(name: LineExtensionAmount::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|LineExtensionAmount $lineExtensionAmount = null;

    #[Since(version: Invoice::VERSION_2_0)]
    #[Type(name: "array<DMT\Ubl\Service\Entity\Invoice\AllowanceCharge>")]
    #[XmlList(
        entry: "AllowanceCharge",
        inline: true,
        namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
    )]
    /** @var array<AllowanceCharge> $allowanceCharge */
    public null|array $allowanceCharge = null;

    #[SerializedName(name: "TaxTotal")]
    #[Type(name: TaxTotal::class)]
    #[Until(version: Invoice::VERSION_1_2)]
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
