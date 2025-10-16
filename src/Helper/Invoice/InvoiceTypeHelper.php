<?php

namespace DMT\Ubl\Service\Helper\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\InvoiceTypeCode;
use DMT\Ubl\Service\List\InvoiceType;

final class InvoiceTypeHelper
{
    public static function fetchFromValue(string|object|null $value): InvoiceTypeCode
    {
        if (!is_object($value) || $value instanceof InvoiceType) {
            $value = (object)['code' => $value];
        }

        $type = $value->code;
        if (!$value->code instanceof InvoiceType) {
            $type = InvoiceType::tryFrom($value->code ?? '');
        }

        $invoiceType = new InvoiceTypeCode();
        $invoiceType->code = $type ?? InvoiceType::Normal;

        if (isset($value->listId)) {
            $invoiceType->listId = $value->listId;
        }

        if (isset($value->listAgencyId)) {
            $invoiceType->listAgencyId = $value->listAgencyId;
        }

        return $invoiceType;
    }
}
