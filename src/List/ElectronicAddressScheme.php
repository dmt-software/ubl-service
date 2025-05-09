<?php

namespace DMT\Ubl\Service\List;

use DMT\Ubl\Service\Entity\Invoice;

enum ElectronicAddressScheme: string
{
    case GLNNumber = '0088';
    case NLCommerceNumber = '0106';
    case BECommerceNumber = '0208';
    case LUCommerceNumber = '0240';
    case BEVatNumber = '9925';
    case LUVatNUmber = '9938';
    case NLVatNumber = '9944';

    public static function lookup(?string $schemeId, string $version = null): ?self
    {
        $testVersions = $version ? [$version] : [Invoice::VERSION_1_0, Invoice::VERSION_2_0];
        foreach (self::cases() as $case) {
            foreach ($testVersions as $testVersion) {
                if ($case->getSchemeId($testVersion) === $schemeId) {
                    return $case;
                }
            }
        }

        return null;
    }

    /**
     * @see https://docs.peppol.eu/edelivery/codelists/v9.1/Peppol%20Code%20Lists%20-%20Participant%20identifier%20schemes%20v9.1.html
     */
    public function getSchemeId(string $version): string
    {
        if (version_compare($version, '2.0', '>=')) {
            return $this->value;
        }

        return match ($this) {
            self::GLNNumber => 'GLN',
            self::NLCommerceNumber => 'NL:KVK',
            self::BECommerceNumber => 'BE:EN',
            self::LUCommerceNumber => 'LU:MAT',
            self::BEVatNumber => 'BE:VAT',
            self::NLVatNumber => 'NL:VAT',
            self::LUVatNUmber => 'LU:VAT',
        };
    }

    public function getSchemeAgencyId(string $version): ?string
    {
        if (version_compare($version, '2.0', '>=')) {
            return null;
        }

        return match ($this) {
            self::GLNNumber => '9',
            default => 'ZZZ'
        };
    }
}
