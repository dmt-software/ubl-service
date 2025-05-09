<?php

namespace DMT\Ubl\Service\Helper\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\DocumentCurrencyCode;

class DocumentCurrencyCodeHelper
{
    public static function fetchFromValue(null|string|object $value): ?DocumentCurrencyCode
    {
        if (!is_object($value)) {
            $value = (object)['code' => $value];
        }

        if (empty($value->code)) {
            return null;
        }

        $currencyCode = new DocumentCurrencyCode();
        $currencyCode->code = $value;

        if (isset($value->listId)) {
            $currencyCode->listId = $value->listId;
        }
        if (isset($value->listAgencyId)) {
            $currencyCode->listAgencyId = $value?->listAgencyId;
        }

        return $currencyCode;
    }
}
