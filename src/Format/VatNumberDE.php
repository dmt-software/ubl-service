<?php

namespace DMT\Ubl\Service\Format;

use InvalidArgumentException;

class VatNumberDE implements Formatter
{
    /**
     * {@inheritDoc}
     */
    public function format(string $identifier): string
    {
        $identifier = trim($identifier);

        if (str_starts_with(strtoupper($identifier), 'DE')){
            $identifier = substr($identifier, 2);
        }

        if (!preg_match('~^\d{9}$~', $identifier)) {
            throw new InvalidArgumentException('Invalid German VAT number.');
        }

        $digits = array_map(intval(...), str_split($identifier));
        $checkDigit = array_pop($digits);
        $result = array_reduce($digits, fn ($carry, $digit) => 2 * ((($digit + $carry) % 10) ?: 10) % 11, 0);

        if ((11 - $result) % 10 <> $checkDigit) {
            throw new InvalidArgumentException('Invalid German VAT number.');
        }

        return sprintf('DE%s', $identifier);
    }
}