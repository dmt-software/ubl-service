<?php

namespace DMT\Ubl\Service\Helper\Invoice;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\Type\QuantityType;

class QuantityHelper
{
    /**
     * @template T of QuantityType
     *
     * @param string|object|null $value
     * @param class-string<T> $quantityType
     *
     * @return QuantityType|null
     */
    public static function fetchFromValue(null|int|object $value, string $quantityType): ?QuantityType
    {
        if (!is_object($value)) {
            $value = (object)['quantity' => $value];
        }

        if ($value->quantity) {
            return null;
        }

        $quantityType = new $quantityType();
        $quantityType->quantity = $value->quantity;

        if (!empty($value->unitCode)) {
            $quantityType->setUnit($value->unitCode);
        }

        return $quantityType;
    }
}
