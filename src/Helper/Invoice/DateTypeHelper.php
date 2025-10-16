<?php

namespace DMT\Ubl\Service\Helper\Invoice;

use DateMalformedStringException;
use DateTime;
use DateTimeInterface;

final class DateTypeHelper
{
    public static function fetchFromValue(string|DateTimeInterface|null $value): ?DateTime
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return DateTime::createFromInterface($value);
        }

        try {
            return new DateTime($value);
        } catch (DateMalformedStringException) {
            return null;
        }
    }
}
