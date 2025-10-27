<?php

namespace DMT\Ubl\Service\Transformer\Invoice;

use DMT\Ubl\Service\Entity\Entity;
use DMT\Ubl\Service\Entity\InvoiceLine as UBLInvoiceLine;
use DMT\Ubl\Service\Objects\InvoiceLine;
use DMT\Ubl\Service\Transformer\EntityToObjectTransformer;
use InvalidArgumentException;

class InvoiceLineToSimpleObjectTransformer implements EntityToObjectTransformer
{
    /**
     * @inheritDoc
     */
    public function transform(Entity|UBLInvoiceLine $entity): InvoiceLine
    {
        if (!$entity instanceof UBLInvoiceLine) {
            throw new InvalidArgumentException('Expected instance of ' . UBLInvoiceLine::class);
        }

        $product = $entity?->item->name ?? $entity?->item?->sellersItemIdentification->id;
        $price = $entity?->price?->priceAmount->amount;
        if ($price === null && $entity?->lineExtensionAmount->amount && $entity?->invoicedQuantity->quantity > 0) {
            $price = $entity->lineExtensionAmount / $entity->invoicedQuantity;
        }

        if (!$product) {
            throw new InvalidArgumentException('No product identification or name found');
        }

        if (!$entity->invoicedQuantity || $price === null) {
            throw new InvalidArgumentException('Expected invoiced quantity and product price');
        }

        $invoiceLine = new InvoiceLine(
            product: $product,
            amount: $entity->invoicedQuantity->quantity,
            price: $price,
            vatPercentage: $entity?->item?->classifiedTaxCategory->percent ?? 21,
        );

        $invoiceLine->lineNumber = $entity?->id;
        $invoiceLine->sku = $entity->item?->sellersItemIdentification->id;

        if ($entity->item?->standardItemIdentification?->id?->schemeId?->value === 'GTIN') {
            $invoiceLine->gtin = $entity->item->standardItemIdentification->id->id;
        }

        return $invoiceLine;
    }
}