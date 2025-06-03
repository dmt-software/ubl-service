<?php

namespace DMT\Ubl\Service\Format;

use InvalidArgumentException;

final class DUNSNumber implements Formatter
{
    /**
     * {@inheritDoc}
     *
     * DUNS number: allowed formats 99-999-9999, 999999999
     */
    public function format(string $identifier): string
    {
        $validated = false;

        $identifier = preg_replace_callback(
            '/^(\d{2})-?(\d{3})-?(\d{4})$/',
            function ($m) use (&$validated) {
                $validated = true;
                return $m[1] . $m[2] . $m[3];
            },
            $identifier
        );

        if (!$validated) {
            throw new InvalidArgumentException('Invalid DUNS number');
        }

        return $identifier;
    }
}