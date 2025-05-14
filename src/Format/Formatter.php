<?php

namespace DMT\Ubl\Service\Format;

interface Formatter
{
    /**
     * Test and format an identifier
     *
     * @param string $identifier
     * @return string
     * @throws \InvalidArgumentException in case the identifier is invalid
     */
    public function format(string $identifier): string;
}
