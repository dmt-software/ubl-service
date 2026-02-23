<?php

namespace DMT\Ubl\Service\Format;

use InvalidArgumentException;

class VatNumberDK implements Formatter
{
    /**
     * @inheritDoc
     */
    public function format(string $identifier): string
    {
        $identifier = trim($identifier);

        if (str_starts_with(strtoupper($identifier), 'DK')){
            $identifier = substr($identifier, 2);
        }

        if (!preg_match('~^\d{8}$~', $identifier)) {
            throw new InvalidArgumentException('Invalid Danish VAT number.');
        }

        $digits = array_map(intval(...), str_split($identifier));
        $weights = [2, 7, 6, 5, 4, 3, 2, 1];
        $sum = array_sum(array_map(array_product(...), array_map(null, $digits, $weights)));

        if ($sum % 11 !== 0) {
            throw new InvalidArgumentException('Invalid Danish VAT number.' .  $identifier);
        }

        return sprintf('DK%s', $identifier);
    }
}