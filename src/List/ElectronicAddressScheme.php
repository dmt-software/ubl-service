<?php

namespace DMT\Ubl\Service\List;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Format\BEVatNumber;
use DMT\Ubl\Service\Format\Formatter;
use DMT\Ubl\Service\Format\GLNNumber;
use DMT\Ubl\Service\Format\GTINNumber;
use DMT\Ubl\Service\Format\KBONumber;
use DMT\Ubl\Service\Format\KvKNumber;
use DMT\Ubl\Service\Format\LUVatNumber;
use DMT\Ubl\Service\Format\NLVatNumber;
use DMT\Ubl\Service\Format\TINNumber;

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
            self::GTINNumber => 'GTIN',
            self::NLCommerceNumber => 'NL:KVK',
            self::BECommerceNumber => 'BE:EN',
            self::LUCommerceNumber => 'LU:MAT',
            self::BEVatNumber => 'BE:VAT',
            self::NLVatNumber => 'NL:VAT',
            self::LUVatNumber => 'LU:VAT',
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
            self::GLNNumber => new GLNNumber(),
            self::GTINNumber => new GTINNumber(),
            self::NLCommerceNumber => new KvKNumber(),
            self::BECommerceNumber => new KBONumber(),
            self::LUCommerceNumber => new TINNumber(),
            self::BEVatNumber => new BEVatNumber(),
            self::NLVatNumber => new NLVatNumber(),
            self::LUVatNumber => new LUVatNumber(),
        };
    }
}
