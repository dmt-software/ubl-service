<?php

namespace DMT\Ubl\Service\Objects;

class Address
{
    /**
     * @param string $address The address (contains street and number).
     * @param string $postcode The zipcode of the address.
     * @param string $city The city of the address.
     * @param string $country The country code for the address.
     */
    public function __construct(
        public string $address,
        public string $postcode,
        public string $city,
        public string $country,
    ) {
    }
}
