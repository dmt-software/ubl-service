<?php

namespace DMT\Ubl\Service\Format;

use DateTime;
use InvalidArgumentException;

class TINNumber implements Formatter
{
    /**
     * {@inheritDoc}
     *
     * The commerce nummers used to engage business in Luxembourg.
     * Allowed formats 19999999999 and 29999999999.
     */
    public function format(string $identifier): string
    {
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
        if (!preg_match('~^(?<year[1-9][0-9]{3})\d{7}$~', $identifier, $matches)) {
            throw new InvalidArgumentException('Invalid Luxembourg TIN number');
        }

        if ($matches['year'] < 1900 || $matches['year'] > date('Y')) {
            throw new InvalidArgumentException('Invalid Luxembourg TIN number');
        }

        /**
         * The 11th digit corresponds to the difference between 11 and the remainder of the division by 11 of the
         * sum of the products obtained by multiplying each of the first 10 digits of the ID number by the
         * respective factors of 5, 4, 3, 2, 7, 6, 5, 4, 3 and 2, being understood that of the numbers generated,
         * during the abovementioned division, a remainder of 1 is not allocated. A remainder of zero during that
         * division is the check digit.
         */

        return $identifier;
    }

    private function validateNaturalPersonNumber(string $identifier): string
    {
        $matches = [];
        if (!preg_match('~^(?<year>[1-2][0-9]{3})(?<month>[0-1][0-9])(?<day>[0-3][0-9])\d{5}$~', $identifier, $matches)) {
            throw new InvalidArgumentException('Invalid Luxembourg TIN number');
        }

        $datetime = DateTime::createFromFormat('Ymd', substr($identifier, 0, 8));

        if ($datetime === false || $datetime > new DateTime()) {
            throw new InvalidArgumentException('Invalid Luxembourg TIN number');
        }

        /**
         * The identification number has 13 digits (9999999999999), the 2 last digits are check digits. The 12 th
         * digit is a check digit calculated on the basis of the algorithm “de Luhn 10”, calculated on the 11 first
         * digits.
         * The 13th digit is a check digit calculated on the basis of the algorithm “de Verhoeff”, calculated on
         * the 11 first digits.
         */

        return $identifier;
    }
}
