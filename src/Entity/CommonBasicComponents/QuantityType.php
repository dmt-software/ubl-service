<?php

namespace DMT\Ubl\Service\Entity\CommonBasicComponents;

interface QuantityType
{
    public const string DEFAULT_UNIT_CODE = 'C62';

    public function setUnit(string $unit): void;
}
