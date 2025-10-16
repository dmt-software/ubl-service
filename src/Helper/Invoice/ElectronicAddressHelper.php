<?php

namespace DMT\Ubl\Service\Helper\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\ElectronicAddressType;
use DMT\Ubl\Service\Entity\Invoice\Type\EndpointId;
use DMT\Ubl\Service\List\ElectronicAddressScheme;

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

        if (isset($value->schemeId) && !$value->schemeId instanceof ElectronicAddressScheme) {
            $value->schemeId = ElectronicAddressScheme::lookup($value->schemeId);
        }

        if ($value->schemeId instanceof ElectronicAddressScheme) {
            $endpointId->schemeId = $value->schemeId;
        }

        if (isset($value->schemeAgencyId)) {
            // @codeCoverageIgnoreStart
            $endpointId->schemeAgencyId = $value->schemeAgencyId;
            // @codeCoverageIgnoreEnd
        }

        return $endpointId;
    }
}
