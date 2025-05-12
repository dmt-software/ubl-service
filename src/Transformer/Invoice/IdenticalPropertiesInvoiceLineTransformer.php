<?php

namespace DMT\Ubl\Service\Transformer\Invoice;

use DMT\Ubl\Service\Entity\Invoice\AllowanceCharge;
use DMT\Ubl\Service\Entity\Invoice\Item;
use DMT\Ubl\Service\Entity\Invoice\Price;
use DMT\Ubl\Service\Entity\Invoice\SellersItemIdentification;
use DMT\Ubl\Service\Entity\Invoice\StandardItemIdentification;
use DMT\Ubl\Service\Entity\Invoice\TaxTotal;
use DMT\Ubl\Service\Entity\Invoice\Type\Amount;
use DMT\Ubl\Service\Entity\Invoice\Type\BaseQuantity;
use DMT\Ubl\Service\Entity\Invoice\Type\InvoicedQuantity;
use DMT\Ubl\Service\Entity\Invoice\Type\LineExtensionAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\PriceAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\TaxAmount;
use DMT\Ubl\Service\Entity\InvoiceLine;
use DMT\Ubl\Service\Helper\Invoice\AmountHelper;
use DMT\Ubl\Service\Helper\Invoice\ElectronicAddressHelper;
use DMT\Ubl\Service\Helper\Invoice\QuantityHelper;
use DMT\Ubl\Service\Transformer\ObjectToEntityTransformer;

class IdenticalPropertiesInvoiceLineTransformer implements ObjectToEntityTransformer
{
    /**
     * @inheritDoc
     */
    public function transform(object $object): InvoiceLine
    {
        $invoiceLine = new InvoiceLine();
        $invoiceLine->id = $object->id ?? null;
        $invoiceLine->invoicedQuantity = QuantityHelper::fetchFromValue($object->number ?? null, InvoicedQuantity::class);
        $invoiceLine->lineExtensionAmount = AmountHelper::fetchFromValue($object->lineExtensionAmount ?? null, LineExtensionAmount::class);
        $invoiceLine->allowanceCharge = $this->renderAllowanceCharges($object->allowanceCharge ?? null);
        $invoiceLine->taxTotal = $this->renderTaxTotal($object->taxTotal ?? null);
        $invoiceLine->item = $this->renderItem($object->item ?? null);
        $invoiceLine->price = $this->renderPrice($object->price ?? null);

        return $invoiceLine;
    }

    private function renderAllowanceCharges(null|object|array $objects): ?array
    {
        if ($objects === null) {
            return null;
        }

        if (is_object($objects)) {
            $objects = [$objects];
        }

        return array_filter(array_map($this->renderAllowanceCharge(...), $objects));
    }

    private function renderAllowanceCharge(null|object $object): ?AllowanceCharge
    {
        if ($object === null) {
            return null;
        }

        $allowanceCharge = new AllowanceCharge();
        $allowanceCharge->allowanceChargeReason = $object->allowanceChargeReason ?? null;
        $allowanceCharge->amount = AmountHelper::fetchFromValue($object->amount ?? null, Amount::class);
        $allowanceCharge->chargeIndicator = $object->chargeIndicator ?? null;
        $allowanceCharge->taxCategory = $object->taxCategory ?? null;

        return $allowanceCharge;
    }

    private function renderTaxTotal(null|object $object): ?TaxTotal
    {
        if ($object?->taxAmount === null) {
            return null;
        }

        $taxTotal = new TaxTotal();
        $taxTotal->taxAmount = AmountHelper::fetchFromValue($object->taxAmount ?? null, TaxAmount::class);

        return $taxTotal;
    }

    private function renderItem(null|object $object): ?Item
    {
        if ($object === null) {
            return null;
        }

        $item = new Item();
        $item->name = $object->name ?? null;

        if (isset($object?->sellersItemIdentification->id)) {
            $item->sellersItemIdentification = new SellersItemIdentification();
            $item->sellersItemIdentification->id = $object->sellersItemIdentification->id;
        }

        $item->standardItemIdentification = $this->renderStandardItemIdentification($object->standardItemIdentification ?? null);

        return $item;
    }

    private function renderStandardItemIdentification(null|object $object): ?StandardItemIdentification
    {
        if ($object?->id === null) {
            return null;
        }

        $standardItemIdentification = new StandardItemIdentification();
        $standardItemIdentification->id = ElectronicAddressHelper::fetchFromValue($object->id ?? null);

        return $standardItemIdentification;
    }

    private function renderPrice(null|object $object): ?Price
    {
        if ($object === null) {
            return null;
        }

        $price = new Price();
        $price->priceAmount = AmountHelper::fetchFromValue($object->priceAmount ?? null, PriceAmount::class);
        $price->baseQuantity = QuantityHelper::fetchFromValue($object->baseQuantity ?? null, BaseQuantity::class);

        return $price;
    }
}
