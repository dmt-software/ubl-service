<?php

namespace DMT\Ubl\Service\Objects;

class InvoiceLine
{
    /**
     * @param string $product The name of the product.
     * @param int $amount The amount of products ordered/invoiced.
     * @param float $price The price of a single product.
     * @param int $vatPercentage The vat percentage to be added.
     * @param int|null $lineNumber The invoice line number.
     * @param string|null $sku The product number given by the seller.
     * @param string|null $gtin The standard (EAN|GTIN) number of the product.
     */
    public function __construct(
        public string $product,
        public int $amount,
        public float $price,
        public int $vatPercentage,
        public null|int $lineNumber = null,
        public null|string $sku = null,
        public null|string $gtin = null,
    ) {
    }
}
