<?php

namespace DMT\Ubl\Service\Format;

use InvalidArgumentException;

final class OrganizationNumberNL implements Formatter
{
    /**
     * {@inheritDoc}
     *
     * Formats numbers registered at logius.nl
     */
    public function format(string $identifier): string
    {
        $identifier = trim($identifier);

        if (!preg_match('/^\d{20}$/', $identifier)) {
            throw new InvalidArgumentException('Invalid Dutch organization number');
        }

        return $identifier;
    }
}