<?php

namespace DMT\Ubl\Service\Helper\Invoice;

use DMT\Ubl\Service\Entity\CommonBasicComponents\CurrencyCode;

final class DocumentCurrencyCodeHelper
{
    public static function fetchFromValue(null|string|object $value): null|CurrencyCode
    {
        if (!is_object($value)) {
            $value = (object)['code' => $value];
        }

        if (empty($value->code)) {
            return null;
        }

        $currencyCode = new CurrencyCode();
        $currencyCode->code = $value->code;

        if (isset($value->listId)) {
            $currencyCode->listId = $value->listId;
        }
        if (isset($value->listAgencyId)) {
            $currencyCode->listAgencyId = $value?->listAgencyId;
        }

        return $currencyCode;
    }
}
