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
use DMT\Ubl\Service\Format\VatNumberDE;
use DMT\Ubl\Service\Format\VatNumberDK;
use DMT\Ubl\Service\Format\VatNumberES;
use DMT\Ubl\Service\Format\VatNumberLU;
use DMT\Ubl\Service\Format\VatNumberNL;
use DMT\Ubl\Service\Format\CommerceNumberLU;

enum ElectronicAddressScheme: string
{
    case DUNSNumber = '0060';
    case GLNNumber = '0088';
    case NLCommerceNumber = '0106';
    case GTINNumber = '0160';
    case NLOrganizationNumber = '0190';
    case DKVatNumber = '0198';
    case BECommerceNumber = '0208';
    case LUCommerceNumber = '0240';
    case ESVatNumber = '9920';
    case BEVatNumber = '9925';
    case DEVatNumber = '9930';
    case LUVatNumber = '9938';
    case NLVatNumber = '9944';
    case DeprecatedDKVatNumber = '9904';
    case DeprecatedNLOrganizationNumber = '9954';


    public static function lookup(null|string $schemeId, string $version = null): null|self
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
     * @see https://docs.peppol.eu/edelivery/codelists/index.html
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
            self::DUNSNumber => 'DUNS',
            self::GLNNumber => 'GLN',
            self::GTINNumber => 'GTIN',
            self::NLCommerceNumber => 'NL:KVK',
            self::BECommerceNumber => 'BE:EN',
            self::LUCommerceNumber => 'LU:MAT',
            self::BEVatNumber => 'BE:VAT',
            self::DEVatNumber => 'DE:VAT',
            self::DKVatNumber => 'DK:ERST',
            self::ESVatNumber => 'ES:VAT',
            self::NLVatNumber => 'NL:VAT',
            self::LUVatNumber => 'LU:VAT',
            self::DeprecatedDKVatNumber => 'DK:SE',
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
            self::BECommerceNumber => new CommerceNumberBE(),
            self::LUCommerceNumber => new CommerceNumberLU(),
            self::NLCommerceNumber => new CommerceNumberNL(),
            self::BEVatNumber => new VatNumberBE(),
            self::DEVatNumber => new VatNumberDE(),
            self::DKVatNumber, self::DeprecatedDKVatNumber => new VatNumberDK(),
            self::ESVatNumber => new VatNumberES(),
            self::LUVatNumber => new VatNumberLU(),
            self::NLVatNumber => new VatNumberNL(),
            self::NLOrganizationNumber, self::DeprecatedNLOrganizationNumber => new OrganizationNumberNL(),
        };
    }
}
