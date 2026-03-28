<?php

namespace DMT\Ubl\Generate;

use DateTime;

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
            'urn:oasis:names:specification:bdndr:schema:xsd:UnqualifiedDataTypes-1' => 'UnqualifiedDataTypes',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2' => 'CommonAggregateComponents',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2' => 'CommonBasicComponents',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2' => 'CommonSignatureComponents',
            'urn:oasis:names:specification:ubl:schema:xsd:QualifiedDataTypes-2' => 'QualifiedDataTypes',
            'urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2' => 'SignatureAggregateComponents',
            'urn:oasis:names:specification:ubl:schema:xsd:SignatureBasicComponents-2' => 'SignatureBasicComponents',
            'urn:oasis:names:specification:ubl:schema:xsd:UnqualifiedDataTypes-2' => 'UnqualifiedDataTypes',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2' => 'CommonExtensionComponents',
            'urn:un:unece:uncefact:data:specification:CoreComponentTypeSchemaModule:2' => 'CoreComponentTypeSchemaModule',
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
        ],
        public array $rawXmlNamespaces = [
            // 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2',
        ],
        public array $namespaceBlacklist = [
            'urn:un:unece:uncefact:documentation:2',
        ]
    ) {
    }
}
