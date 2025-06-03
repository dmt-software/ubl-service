<?php

namespace DMT\Ubl\Service\Format;

use InvalidArgumentException;

final readonly class GlobalTradeItemNumber implements Formatter
{
    public function __construct(private null|int $size = null)
    {
    }

    /**
     * {@inheritDoc}
     *
     * GTIN numbers are 8, 12, 13, 14 or 17, 18 digits long. GTIN-13 is also known as GLN (global location number)
     * or EAN (European article number).
     */
    public function format(string $identifier): string
    {
        $identifier = trim($identifier);

        $size = strlen($identifier);
        if ($size <> preg_match('~\d~', $identifier) || !in_array($size, [8, 12, 13, 14, 17, 18], true)) {
            throw new InvalidArgumentException('Invalid GTIN number');
        }

        if ($this->size && $this->size <> $size) {
            throw new InvalidArgumentException("Invalid GTIN-$this->size number");
        }

        $digits = array_map('intval', str_split($identifier));
        $total = ($size % 2 == 1) ? array_shift($digits) : 0;

        foreach (array_chunk($digits, 2) as [$a, $b]) {
            $total += $a * 3 + $b;
        }

        if ($total % 10 > 0) {
            throw new InvalidArgumentException("Invalid GTIN number");
        }

        return $identifier;
    }
}
