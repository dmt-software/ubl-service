<?php

namespace DMT\Ubl\Service\Entity\Invoice\Type;

interface AmountType
{
    public function setCurrency(string $currency): void;
}
