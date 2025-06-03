<?php

namespace DMT\Ubl\Service\Format;

use InvalidArgumentException;

final class GlobalLocationNumber implements Formatter
{
    /**
     * {@inheritDoc}
     *
     * Check for global location number (GTIN-13), format 1234567890123
     *
     * @see GlobalTradeItemNumber
     */
    public function format(string $identifier): string
    {
        $identifier = trim($identifier);

        try {
            return (new GlobalTradeItemNumber(13))->format($identifier);
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException('Invalid GLN number');
        }
    }
}
