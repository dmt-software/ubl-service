<?php

namespace DMT\Ubl\Service\Format;

use InvalidArgumentException;

class GLNNumber implements Formatter
{
    /**
     * {@inheritDoc}
     *
     * Check for global location number (GTIN-13), format 1234567890123
     *
     * @see GTINNumber
     */
    public function format(string $identifier): string
    {
        try {
            return (new GTINNumber(13))->format($identifier);
        } catch (InvalidArgumentException) {
            throw new InvalidArgumentException('Invalid GLN number');
        }
    }
}
