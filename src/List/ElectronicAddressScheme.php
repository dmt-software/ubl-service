<?php

namespace DMT\Ubl\Service\List;

enum ElectronicAddressScheme: string
{
    case GLNNumber = '0088';
    case NLCommerceNumber = '0106';
    case BEVatNumber = '9925';
    case NLVatNumber = '9944';

    public function getSchemeId(string $version): string
    {
        if (version_compare($version, '2.0', '>=')) {
            return $this->value;
        }

        return match ($this) {
            self::GLNNumber => 'GLN',
            self::NLCommerceNumber => 'NL:KVK',
            self::NLVatNumber => 'NL:VAT',
            self::BEVatNumber => 'BE:VAT',
        };
    }

    public function getSchemeAgencyId(string $version): ?string
    {
        if (version_compare($version, '2.0', '>=')) {
            return null;
        }

        return match ($this) {
            self::GLNNumber => '9',
            self::NLCommerceNumber => '82',
            default => 'ZZZ'
        };
    }
}
