<?php

namespace DMT\Ubl\Service\Format;

use InvalidArgumentException;

final class VatNumberNL implements Formatter
{
    /**
     * {@inheritDoc}
     *
     * Format NL123456789B01
     */
    public function format(string $identifier): string
    {
        $identifier = strtoupper(trim($identifier));

        if (!str_starts_with($identifier, 'NL')){
            $identifier = 'NL' . $identifier;
        }

        $matches = [];
        if (!preg_match('~^NL(?<rsin>[0-9]{9})B[0-9]{2}$~', $identifier, $matches)) {
            throw new InvalidArgumentException('Invalid Dutch VAT number');
        }

        if ($this->test11($matches['rsin'])) {
            return $identifier;
        }

        if (!$this->mod97($identifier)) {
            throw new InvalidArgumentException('Invalid Dutch VAT number');
        }

        return $identifier;
    }

    /**
     * Test modulo 97 for sole proprietaries or self-employments.
     */
    private function mod97(string $identifier): bool
    {
        return ((int) preg_replace_callback('~[A-Z]~', fn ($m) => strval(ord($m[0]) - 55), $identifier) % 97) === 1;
    }

    /**
     * Original test for companies.
     */
    private function test11(string $rsin): bool
    {
        $digits = array_map('intval', str_split($rsin));
        $checkDigit = array_pop($digits);

        $t = 0;
        $i = 1;
        foreach (array_reverse($digits) as $digit) {
            $t += (++$i * $digit);
        }

        return ($t % 11) === $checkDigit;
    }
}
