<?php

namespace DMT\Ubl\Service\Helper\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\AmountType;

final class AmountHelper
{
    /**
     * @template T of AmountType
     *
     * @param string|object|null $value
     * @param class-string<T> $amountType
     *
     * @return AmountType|null
     */
    public static function fetchFromValue(mixed $value, string $amountType): null|AmountType
    {
        if (is_scalar($value)) {
            $value = (object)['amount' => $value];
        }

        if (!is_numeric($value->amount)) {
            return null;
        }

        $amountType = new $amountType();
        $amountType->amount = $value->amount;

        if (isset($value->currencyId)) {
            $amountType->setCurrency($value->currencyId);
        }

        return $amountType;
    }
}