<?php

namespace DMT\Ubl\Service\Format;

use InvalidArgumentException;

class BEVatNumber implements Formatter
{
    /**
     * {@inheritDoc}
     *
     * BE Vat number consists of KBO number prefixed with BE.
     */
    public function format(string $identifier): string
    {
        if (str_starts_with(strtoupper($identifier), 'BE')){
            $identifier = substr($identifier, 2);
        }

        try {
            $identifier = (new KBONumber())->format($identifier);
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException('Invalid Belgium VAT number');
        }

        return sprintf('BE%s', $identifier);
    }
}