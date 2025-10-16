<?php

namespace DMT\Ubl\Service\Helper\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\IdentificationCode;

final class IdentificationCodeHelper
{
    public static function fetchFromValue(null|string|object $value): null|IdentificationCode
    {
        if (!is_object($value)) {
            $value = (object)['code' => $value];
        }

        if (empty($value->code)) {
            return null;
        }

        $identificationCode = new IdentificationCode();
        $identificationCode->code = $value->code;

        if (isset($value->listId)) {
            $identificationCode->listId = $value?->listId;
        }

        if (isset($value->listAgencyId)) {
            $identificationCode->listAgencyId = $value->listAgencyId;
        }

        return $identificationCode;
    }
}
