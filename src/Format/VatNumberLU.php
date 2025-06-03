<?php

namespace DMT\Ubl\Service\Format;

final class VatNumberLU implements Formatter
{
    /**
     * {@inheritDoc}
     *
     * Format LU12345678
     */
    public function format(string $identifier): string
    {
        $identifier = trim($identifier);

        if (str_starts_with(strtoupper($identifier), 'LU')) {
            $identifier = substr($identifier, 2);
        }

        if (!preg_match('~^\d{8}$~', $identifier) || ((int)substr($identifier, 0, -2) % 89) != substr($identifier, -2)) {
            throw new \InvalidArgumentException('Invalid Luxembourg VAT number format');
        }

        return sprintf('LU%s', $identifier);
    }
}
