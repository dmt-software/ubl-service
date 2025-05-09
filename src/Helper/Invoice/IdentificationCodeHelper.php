<?php

namespace DMT\Ubl\Service\Helper\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\IdentificationCode;

class IdentificationCodeHelper
{
    public static function fetchFromValue(null|string|object $value): ?IdentificationCode
    {
        if (!is_object($value)) {
            $value = (object)['code' => $value];
        }
        if (empty($value->code)) {
            return null;
        }

        $identificationCode = new IdentificationCode();
        $identificationCode->code = $value->code;

        if ($value?->listId) {
            $identificationCode->listId = $value?->listId;
        }
        if ($value?->listAgencyId) {
            $identificationCode->listAgencyId = $value->listAgencyId;
        }

        return $identificationCode;
    }
}
