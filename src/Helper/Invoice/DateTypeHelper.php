<?php

namespace DMT\Ubl\Service\Helper\Invoice;

use DateTime;
use DateTimeInterface;

class DateTypeHelper
{
    public static function fetchFromValue(string|DateTimeInterface|null $value): ?DateTime
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return DateTime::createFromInterface($value);
        }

        return new DateTime($value);
    }
}
