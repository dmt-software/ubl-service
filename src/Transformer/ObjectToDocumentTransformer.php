<?php

namespace DMT\Ubl\Service\Transformer;

use DMT\Ubl\Service\Entity\Document;
use RuntimeException;

interface ObjectToDocumentTransformer
{
    /**
     * Transform a custom object into a UBL document for serialization.
     *
     * @param object $object The object to transform
     * @return Document The UBL document to transform the object into
     * @throws RuntimeException When the object can not be transformed
     */
    public function transform(object $object): Document;
}
