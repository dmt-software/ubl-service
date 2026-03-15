<?php

namespace DMT\Ubl\Service\Entity;

interface Document extends Entity
{
    public function findLines(): array;
}