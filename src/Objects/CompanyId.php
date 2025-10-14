<?php

namespace DMT\Ubl\Service\Objects;

class CompanyId
{
    /**
     * @param string $id The id used for identification.
     * @param string $schemeId The type of identification used.
     */
    public function __construct(
        public string $id,
        public string $schemeId,
    ) {
    }
}
