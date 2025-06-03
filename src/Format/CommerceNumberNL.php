<?php

namespace DMT\Ubl\Service\Format;

use InvalidArgumentException;

final class CommerceNumberNL implements Formatter
{
    /**
     * {@inheritDoc}
     *
     * The commerce numbers in the Netherlands, in 12345678 format.
     */
    public function format(string $identifier): string
    {
        $identifier = trim($identifier);

        if (!preg_match('~^\d{8}$~', $identifier)) {
            throw new InvalidArgumentException('Invalid KvK number format');
        }

        return $identifier;
    }
}
