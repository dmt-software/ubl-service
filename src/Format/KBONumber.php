<?php

namespace DMT\Ubl\Service\Format;

use InvalidArgumentException;

class KBONumber implements Formatter
{
    /**
     * {@inheritDoc}
     *
     * The Belgium commerce numbers.
     * Allowed formats 0123456789, 1234.123.123 or 0123 123 123
     */
    public function format(string $identifier): string
    {
        $validated = false;

        $identifier = preg_replace_callback(
            '/^[0-1]?[0-9]{3}([\.|\s]?)[0-9]{3}(\1)[0-9]{3}$/',
            function ($m) use (&$validated) {
                $validated = true;
                return sprintf('%010s', str_replace($m[1], '', $m[0]));
            },
            $identifier
        );

        if (!$validated || ((int) substr($identifier, 0, -2) + (int) substr($identifier, -2)) % 97 > 0) {
            throw new InvalidArgumentException('Invalid KBO number');
        }

        return $identifier;
    }
}
