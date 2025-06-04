<?php

namespace DMT\Ubl\Service\Format;

use DateTime;
use InvalidArgumentException;

final class CommerceNumberLU implements Formatter
{
    /**
     * {@inheritDoc}
     *
     * The commerce nummers, called TIN, used to engage business in Luxembourg.
     * Allowed formats 19999999999 and 29999999999.
     */
    public function format(string $identifier): string
    {
        $identifier = trim($identifier);

        $len = strlen($identifier);

        if ($len <> 11 && $len <> 13) {
            throw new InvalidArgumentException('Invalid Luxembourg TIN number');
        }

        return match ($len) {
            11 => $this->validateCompanyNumber($identifier),
            13 => $this->validateNaturalPersonNumber($identifier)
        };
    }

    private function validateCompanyNumber(string $identifier): string
    {
        $matches = [];
        if (!preg_match('~^(?<year>[1-9][0-9]{3})\d{7}$~', $identifier, $matches)) {
            throw new InvalidArgumentException('Invalid Luxembourg TIN number');
        }

        if ((new \DateTime('+ 1 year'))->format('Y') < $matches['year']) {
            throw new InvalidArgumentException('Incorrect TIN number');
        }

        $weights = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
        $digits = array_map(intval(...), str_split($identifier));
        $checkDigit = array_pop($digits);

        $digit = 11 - (array_sum(array_map(array_product(...), array_map(null, $digits, $weights))) % 11);

        if ($digit != $checkDigit || $digit == 1) {
            throw new InvalidArgumentException('Incorrect TIN number');
        }

        return $identifier;
    }

    private function validateNaturalPersonNumber(string $identifier): string
    {
        if (!preg_match('~^(?<year>[1-2][0-9]{3})(?<month>[0-1][0-9])(?<day>[0-3][0-9])\d{5}$~', $identifier)) {
            throw new InvalidArgumentException('Invalid Luxembourg TIN number');
        }

        $datetime = DateTime::createFromFormat('Ymd', substr($identifier, 0, 8));

        if ($datetime === false || $datetime > new DateTime() || $datetime->format('Y') < 1900) {
            throw new InvalidArgumentException('Invalid Luxembourg TIN number');
        }


        $digits = array_map(intval(...), str_split($identifier));
        $digitPairs = array_chunk(array_merge(array_slice($digits, 0, 11), [0]), 2);

        $sum = 0;
        foreach ($digitPairs as &$pair) {
            $pair[0] *= 2;
            if ($pair[0] > 9) {
                $pair[0] -= 9;
            }
            $sum += array_sum($pair);
        }

        if (($sum + $digits[11]) % 10 != 0) {
            throw new InvalidArgumentException('Invalid Luxembourg TIN number');
        }

        /**
         * Not implemented:
         *
         * The 13th digit is a check digit calculated on the basis of the algorithm “de Verhoeff”, calculated on
         * the 11 first digits.
         */

        return $identifier;
    }
}
