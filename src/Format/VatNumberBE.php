<?php

namespace DMT\Ubl\Service\Format;

use InvalidArgumentException;

final class VatNumberBE implements Formatter
{
    /**
     * {@inheritDoc}
     *
     * BE Vat number consists of KBO number prefixed with BE.
     */
    public function format(string $identifier): string
    {
        $identifier = trim($identifier);

        if (str_starts_with(strtoupper($identifier), 'BE')){
            $identifier = substr($identifier, 2);
        }

        try {
            $identifier = (new CommerceNumberBE())->format($identifier);
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException('Invalid Belgium VAT number');
        }

        return sprintf('BE%s', $identifier);
    }
}