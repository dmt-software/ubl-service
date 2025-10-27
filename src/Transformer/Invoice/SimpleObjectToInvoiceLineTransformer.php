<?php

namespace DMT\Ubl\Service\Transformer\Invoice;

use DMT\Ubl\Service\Entity\Entity;
use DMT\Ubl\Service\Entity\Invoice\ClassifiedTaxCategory;
use DMT\Ubl\Service\Entity\Invoice\Item;
use DMT\Ubl\Service\Entity\Invoice\Price;
use DMT\Ubl\Service\Entity\Invoice\SellersItemIdentification;
use DMT\Ubl\Service\Entity\Invoice\StandardItemIdentification;
use DMT\Ubl\Service\Entity\Invoice\Type\BaseQuantity;
use DMT\Ubl\Service\Entity\Invoice\Type\Id;
use DMT\Ubl\Service\Entity\Invoice\Type\InvoicedQuantity;
use DMT\Ubl\Service\Entity\Invoice\Type\LineExtensionAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\PriceAmount;
use DMT\Ubl\Service\Entity\InvoiceLine;
use DMT\Ubl\Service\Helper\Invoice\AmountHelper;
use DMT\Ubl\Service\Helper\Invoice\ElectronicAddressHelper;
use DMT\Ubl\Service\Helper\Invoice\QuantityHelper;
use DMT\Ubl\Service\Objects\InvoiceLine as InvoiceLineDTO;
use DMT\Ubl\Service\Transformer\ObjectToEntityTransformer;
use InvalidArgumentException;

class SimpleObjectToInvoiceLineTransformer implements ObjectToEntityTransformer
{
    public function transform(object $object): Entity
    {
        if (!$object instanceof InvoiceLineDTO) {
            throw new InvalidArgumentException('Expected instance of ' . InvoiceLineDTO::class);
        }

        if (!$object->sku && !$object->gtin) {
            throw new InvalidArgumentException('No product identification given');
        }

        $invoiceLine = new InvoiceLine();
        $invoiceLine->id = $object->lineNumber;
        $invoiceLine->invoicedQuantity = QuantityHelper::fetchFromValue($object->amount, InvoicedQuantity::class);
        $invoiceLine->lineExtensionAmount = AmountHelper::fetchFromValue($object->price * $object->amount, LineExtensionAmount::class);
        $invoiceLine->item = new Item();
        $invoiceLine->item->classifiedTaxCategory = new ClassifiedTaxCategory();
        $invoiceLine->item->classifiedTaxCategory->percent = $object->vatPercentage;
        $invoiceLine->price = new Price();
        $invoiceLine->price->priceAmount = AmountHelper::fetchFromValue($object->price, PriceAmount::class);
        $invoiceLine->price->baseQuantity = QuantityHelper::fetchFromValue(1, BaseQuantity::class);

        if ($object->sku) {
            $invoiceLine->item->name = $object->product;
            $invoiceLine->item->sellersItemIdentification = new SellersItemIdentification();
            $invoiceLine->item->sellersItemIdentification->id = ElectronicAddressHelper::fetchFromValue($object->sku, Id::class);
        }

        if ($object->gtin) {
            $invoiceLine->item->standardItemIdentification = new StandardItemIdentification();
            $invoiceLine->item->standardItemIdentification->id = ElectronicAddressHelper::fetchFromValue($object->gtin, Id::class);
            $invoiceLine->item->standardItemIdentification->id->schemeId = 'GTIN';
        }

        return $invoiceLine;
    }
}