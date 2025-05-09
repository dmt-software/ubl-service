<?php

namespace DMT\Ubl\Service\Helper\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\InvoiceTypeCode;
use DMT\Ubl\Service\List\InvoiceType;

class InvoiceTypeHelper
{
    public static function fetchFromValue(mixed $value): InvoiceTypeCode
    {
        if (is_null($value) || is_object($value) || is_scalar($value)) {
            $type = InvoiceType::tryFrom(is_scalar($value) ? (string) $value : (string) $value->code ?? '');
        }

        $invoiceType = new InvoiceTypeCode();
        $invoiceType->code = $type ?? null;

        if (isset($value->listId)) {
            $invoiceType->listId = $value->listId;
        }
        if (isset($value->listAgencyId)) {
            $invoiceType->listAgencyId = $value->listAgencyId;
        }

        return $invoiceType;
    }
}
