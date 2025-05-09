<?php

namespace DMT\Ubl\Service\Transformer;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\UblEntity;
use RuntimeException;

interface ObjectToUBLEntityTransformer
{
    /**
     * Transform a custom object into a UBL entity for serialization.
     *
     * @param object $object The object to transform
     * @return Invoice The UBL entity to transform the object into (currently only invoice is supported)
     * @throws RuntimeException When the object can not be transformed
     */
    public function transform(object $object): UblEntity;
}
