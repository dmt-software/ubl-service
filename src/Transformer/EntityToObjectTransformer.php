<?php

namespace DMT\Ubl\Service\Transformer;

use DMT\Ubl\Service\Entity\Entity;
use DMT\Ubl\Service\Entity\Invoice;
use RuntimeException;

interface EntityToObjectTransformer
{
    /**
     * Transform an UBL entity into a custom object for further processing.
     *
     * @param Invoice $entity The entity to transform (currently only an invoice is supported)
     * @return object The object representing the entity
     * @throws RuntimeException When the invoice can not be transformed
     */
    public function transform(Entity $entity): object;
}
