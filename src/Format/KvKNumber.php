<?php

namespace DMT\Ubl\Service\Format;

class KvKNumber implements Formatter
{
    /**
     * {@inheritDoc}
     *
     * The commerce numbers in the Netherlands, in 12345678 format.
     */
    public function format(string $identifier): string
    {
        if (!preg_match('~^\d{8}$~', $identifier)) {
            throw new \InvalidArgumentException('Invalid KvK number format');
        }

        return $identifier;
    }
}
