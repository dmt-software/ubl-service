<?php

namespace DMT\Ubl\Service\Objects;

class Party
{
    /**
     * @param CompanyId $identification The id to identify the company.
     * @param string $companyName The company name.
     * @param string $address The postal address of the company.
     * @param string $postcode The zipcode of the company's address.
     * @param string $city The city of the company's address.
     * @param string $country The postal address country code.
     * @param string|null $vatNumber The vat number of the company (required for AccountingSupplierParty).
     * @param string|null $companyLegalName The legal name for the company.
     * @param string|null $contact The name of person to contact.
     * @param string|null $phone THe phone number of the contact.
     * @param string|null $email The email address of the contact.
     */
    public function __construct(
        public CompanyId $identification,
        public string $companyName,
        public string $address,
        public string $postcode,
        public string $city,
        public string $country,
        public null|string $vatNumber = null,
        public null|string $companyLegalName = null,
        public null|string $contact = null,
        public null|string $phone = null,
        public null|string $email = null,
    ) {
    }
}
