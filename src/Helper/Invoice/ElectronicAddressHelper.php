<?php

namespace DMT\Ubl\Service\Helper\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\ElectronicAddressType;
use DMT\Ubl\Service\Entity\Invoice\Type\EndpointId;

final class ElectronicAddressHelper
{
    /**
     * @template T
     *
     * @param string|object|null $value
     * @param class-string<T> $type
     * @return T|null
     */
    public static function fetchFromValue(null|string|object $value, string $type = EndpointId::class): ?ElectronicAddressType
    {
        if (!is_object($value)) {
            $value = (object)['id' => $value];
        }

        if (empty($value->id)) {
            return null;
        }

        $endpointId = new $type();
        $endpointId->id = $value->id;

        if (isset($value->schemeId)) {
            $endpointId->schemeId = $value->schemeId;
        }
        if (isset($value->schemeAgencyId)) {
            $endpointId->schemeAgencyId = $value->schemeAgencyId;
        }

        return $endpointId;
    }
}
