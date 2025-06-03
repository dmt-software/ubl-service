<?php

namespace DMT\Ubl\Service\List;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Format\OrganizationNumberNL;
use DMT\Ubl\Service\Format\VatNumberBE;
use DMT\Ubl\Service\Format\Formatter;
use DMT\Ubl\Service\Format\GlobalLocationNumber;
use DMT\Ubl\Service\Format\GlobalTradeItemNumber;
use DMT\Ubl\Service\Format\CommerceNumberBE;
use DMT\Ubl\Service\Format\CommerceNumberNL;
use DMT\Ubl\Service\Format\VatNumberLU;
use DMT\Ubl\Service\Format\VatNumberNL;
use DMT\Ubl\Service\Format\CommerceNumberLU;

enum ElectronicAddressScheme: string
{
    case GLNNumber = '0088';
    case NLCommerceNumber = '0106';
    case GTINNumber = '0160';
    case BECommerceNumber = '0208';
    case LUCommerceNumber = '0240';
    case BEVatNumber = '9925';
    case LUVatNumber = '9938';
    case NLVatNumber = '9944';
    case NLOrganizationNumber = '0190';
    case DeprecatedNLOrganizationNumber = '9954';

    private const string DEPRECATED_NL_ORGANIZATION_NUMBER = 'NL:OIN';

    public static function lookup(?string $schemeId, string $version = null): ?self
    {
        $testVersions = $version ? [$version] : [Invoice::VERSION_1_1, Invoice::VERSION_2_0];
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
        if ($this == self::DeprecatedNLOrganizationNumber && $version !== Invoice::VERSION_1_0) {
            return self::NLOrganizationNumber->getSchemeId($version);
        }

        if (version_compare($version, '2.0', '>=')) {
            return $this->value;
        }

        return match ($this) {
            self::GLNNumber => 'GLN',
            self::GTINNumber => 'GTIN',
            self::NLCommerceNumber => 'NL:KVK',
            self::BECommerceNumber => 'BE:EN',
            self::LUCommerceNumber => 'LU:MAT',
            self::BEVatNumber => 'BE:VAT',
            self::NLVatNumber => 'NL:VAT',
            self::LUVatNumber => 'LU:VAT',
            self::NLOrganizationNumber => 'NL:OINO',
            self::DeprecatedNLOrganizationNumber => 'NL:OIN',
        };
    }

    public function getSchemeAgencyId(string $version): ?string
    {
        if (version_compare($version, '2.0', '>=')) {
            return null;
        }

        return match ($this) {
            self::GLNNumber,
            self::GTINNumber => '9',
            default => 'ZZZ'
        };
    }

    public function getFormatter(): Formatter
    {
        return match ($this) {
            self::GLNNumber => new GlobalLocationNumber(),
            self::GTINNumber => new GlobalTradeItemNumber(),
            self::NLCommerceNumber => new CommerceNumberNL(),
            self::BECommerceNumber => new CommerceNumberBE(),
            self::LUCommerceNumber => new CommerceNumberLU(),
            self::BEVatNumber => new VatNumberBE(),
            self::NLVatNumber => new VatNumberNL(),
            self::LUVatNumber => new VatNumberLU(),
            self::NLOrganizationNumber,
            self::DeprecatedNLOrganizationNumber => new OrganizationNumberNL(),
        };
    }
}
