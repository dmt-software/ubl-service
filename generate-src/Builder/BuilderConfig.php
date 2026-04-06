<?php

namespace DMT\Ubl\Generate\Builder;

use DateTime;
use DOMElement;

final class BuilderConfig
{
    public function __construct(
        public string $path,
        public string $namespace = 'DMT\\Ubl\\Generated',
        public array $propertyNameReplace = [
            '~^ubl~i' => 'ubl',
            '~id$~i' => 'Id',
            '~^uuid$~i' => 'uuid',
            '~^Id$~' => 'id',
        ],
        public array $phpNamespaces = [
            'urn:oasis:names:specification:bdndr:schema:xsd:UnqualifiedDataTypes-1' => 'Types',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2' => 'CommonAggregateComponents',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2' => 'CommonBasicComponents',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2' => 'CommonSignatureComponents',
            'urn:oasis:names:specification:ubl:schema:xsd:QualifiedDataTypes-2' => 'QualifiedDataTypes',
            'urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2' => 'SignatureAggregateComponents',
            'urn:oasis:names:specification:ubl:schema:xsd:SignatureBasicComponents-2' => 'SignatureBasicComponents',
            'urn:oasis:names:specification:ubl:schema:xsd:UnqualifiedDataTypes-2' => 'Types',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2' => 'CommonExtensionComponents',
            'http://uri.etsi.org/01903/v1.3.2#' => 'Etsi13',
            'http://uri.etsi.org/01903/v1.4.1#' => 'Etsi14',
            'http://www.w3.org/2000/09/xmldsig#'=> 'Dsigxx',
            'http://www.w3.org/2009/xmldsig11#' => 'Dsig11',
        ],
        // sort these scalar-first
        public array $phpTypeMap = [
            'http://www.w3.org/2001/XMLSchema' => [
                'ID' => ['string'],
                'anyURI' => ['string'],
                'base64Binary' => ['string'],
                'boolean' => ['bool'],
                'date' => [DateTime::class],
                'dateTime' => [DateTime::class],
                'decimal' => ['float'],
                'double' => ['float'],
                'float' => ['float'],
                'integer' => ['int'],
                'language' => ['string'],
                'normalizedString' => ['string'],
                'positiveInteger' => ['int'],
                'string' => ['string'],
                'time' => [DateTime::class],
            ],
            'urn:oasis:names:specification:ubl:schema:xsd:UnqualifiedDataTypes-2' => [
                'IndicatorType' => ['bool'],
                'DateType' => [DateTime::class],
                'DateTimeType' => [DateTime::class],
                'TimeType' => [DateTime::class],
            ],
            'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2' => [
                'ExtensionContentType' => [DOMElement::class],
            ],
            'http://www.w3.org/2000/09/xmldsig#' => [
                'SignatureType' => [DOMElement::class],
            ],
        ],
        public array $jmsTypeMap = [
            'http://www.w3.org/2001/XMLSchema' => [
                'ID' => 'string',
                'anyURI' => 'string',
                'base64Binary' => 'string',
                'boolean' => 'bool',
                'date' => "DateTime<'Y-m-d'>",
                'dateTime' => "DateTime<'Y-m-d H:i:s'>",
                'decimal' => 'float',
                'double' => 'float',
                'float' => 'float',
                'integer' => 'int',
                'language' => 'string',
                'normalizedString' => 'string',
                'positiveInteger' => 'int',
                'string' => 'string',
                'time' => "DateTime<'H:i:s'>",
            ],
            'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2' => [
                'ExtensionContentType' => 'RawXml',
            ],
            'http://www.w3.org/2000/09/xmldsig#' => [
                'SignatureType' => 'RawXml',
            ],
        ],
        public array $namespaceBlacklist = [
            'http://uri.etsi.org/01903/v1.3.2#',
            'http://uri.etsi.org/01903/v1.4.1#',
            'http://www.w3.org/2000/09/xmldsig#',
            'http://www.w3.org/2009/xmldsig11#',
            'urn:un:unece:uncefact:documentation:2',
            'urn:un:unece:uncefact:data:specification:CoreComponentTypeSchemaModule:2',
        ],
        public bool $addDocumentation = true,
        public array $elementBlacklist = [

        ],
        public array $typeBlacklist = [
            'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2' => [
                'UBLExtensionType', // UBL-CR-001
                'UBLExtensionsType', // UBL-CR-001
            ],
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2' => [
                'ProfileExecutionIDType', // UBL-CR-003
                'CopyIndicatorType', // UBL-CR-004
                'UUIDType', // UBL-CR-005
                'IssueTimeType', // UBL-CR-006
                'PricingCurrencyCodeType', // UBL-CR-007
                'PaymentCurrencyCodeType', // UBL-CR-008
                'PaymentAlternativeCurrencyCodeType', // UBL-CR-009
                'AccountingCostCodeType', // UBL-CR-010
                'LineCountNumericType', // UBL-CR-011
            ]
        ],
        public array $attributeBlacklist = [
            'schemeName', // UBL-DT-08
            'schemeAgencyName', // UBL-DT-09
            'schemeDataURI', // UBL-DT-10
            'schemeURI', // UBL-DT-11
            'format', // UBL-DT-12
            'unitCodeListIdentifier', // UBL-DT-13
            'unitCodeListAgencyIdentifier', // UBL-DT-14
            'unitCodeListAgencyName', // UBL-DT-15
            'listAgencyName', // UBL-DT-16
            'listName', // UBL-DT-17
            'languageID', // UBL-DT-19
            'listURI', // UBL-DT-20
            'listSchemeURI', // UBL-DT-21
            'languageLocaleID', // UBL-DT-22
            'uri', // UBL-DT-23
            'currencyCodeListVersionID', // UBL-DT-24
            'characterSetCode', // UBL-DT-25
            'encodingCode', // UBL-DT-26
        ]
    ) {
    }
}
