<?php

namespace DMT\Ubl\Service\Transformer;

use DMT\Ubl\Service\Entity\Document;
use DMT\Ubl\Service\Entity\Entity;
use RuntimeException;

interface DocumentToObjectTransformer
{
    /**
     * Transform an UBL document into a custom object for further processing.
     *
     * @param Document $document The entity to transform
     * @return object The object representing the entity
     * @throws RuntimeException When the invoice can not be transformed
     */
    public function transform(Document $document): object;
}
