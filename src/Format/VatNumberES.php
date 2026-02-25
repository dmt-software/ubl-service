<?php

namespace DMT\Ubl\Service\Format;

use InvalidArgumentException;

class VatNumberES implements Formatter
{
    /**
     * @inheritDoc
     */
    public function format(string $identifier): string
    {
        $identifier = strtoupper(trim($identifier));

        if (str_starts_with($identifier, 'ES')){
            $identifier = substr($identifier, 2);
        }

        if (!preg_match('~^[0-9A-Z]((?<![ITO])\d{7})[0-9A-Z]$~', $identifier)) {
            throw new InvalidArgumentException('Invalid Spanish VAT number.');
        }

        if (preg_match('~^[0-9K-MX-Z]~', $identifier)) {
            $this->validatePersonalNumber($identifier);
        } else {
            $this->validateEntityNumber($identifier);
        }

        return sprintf('ES%s', $identifier);
    }

    private function validatePersonalNumber(string $identifier): void
    {
        $checkCharacters = str_split('TRWAGMYFPDXBNJZSQVHLCKE');
        $checkCharacter = substr($identifier, -1);

        $identifier = preg_replace_callback('~^([X-Z])~', fn ($m) => ord($m[1]) - 88, $identifier);

        $count = is_numeric(substr($identifier, 0, 1)) ? 0 : 1;
        $result = $checkCharacters[substr($identifier, $count, -1) % 23];

        if ($checkCharacter !== $result)  {
            throw new InvalidArgumentException('Invalid Spanish VAT number.');
        }
    }

    private function validateEntityNumber(string $identifier): void
    {
        $digits = array_map(intval(...), str_split(substr($identifier, 1, -1)));
        $multipliers = [2, 1, 2, 1, 2, 1, 2];
        $checkDigit = substr($identifier, -1);

        if (!is_numeric($checkDigit)) {
            $checkDigit = ord($checkDigit) - 65;
        }
        $checkDigit = $checkDigit % 10;


        $result = array_reduce(
            array_map(array_product(...), array_map(null, $digits, $multipliers)),
            fn ($carry, $digit) => $carry + floor($digit / 10) + $digit % 10
        );
        $result = (10 - $result % 10) % 10;

        if ($checkDigit !== $result)  {
            throw new InvalidArgumentException('Invalid Spanish VAT number.');
        }
    }
}
