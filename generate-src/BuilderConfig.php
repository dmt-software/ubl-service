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
            'urn:oasis:names:specification:bdndr:schema:xsd:UnqualifiedDataTypes-1' => 'DMT\\Ubl\\Generated\\UnqualifiedDataTypesV1',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2' => 'DMT\\Ubl\\Generated\\CommonAggregateComponentsV2',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2' => 'DMT\\Ubl\\Generated\\CommonBasicComponentsV2',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonSignatureComponents-2' => 'DMT\\Ubl\\Generated\\CommonSignatureComponentsV2',
            'urn:oasis:names:specification:ubl:schema:xsd:QualifiedDataTypes-2' => 'DMT\\Ubl\\Generated\\QualifiedDataTypesV2',
            'urn:oasis:names:specification:ubl:schema:xsd:SignatureAggregateComponents-2' => 'DMT\\Ubl\\Generated\\SignatureAggregateComponentsV2',
            'urn:oasis:names:specification:ubl:schema:xsd:SignatureBasicComponents-2' => 'DMT\\Ubl\\Generated\\SignatureBasicComponentsV2',
            'urn:oasis:names:specification:ubl:schema:xsd:UnqualifiedDataTypes-2' => 'DMT\\Ubl\\Generated\\UnqualifiedDataTypesV2',
            'urn:un:unece:uncefact:data:specification:CoreComponentTypeSchemaModule:2' => 'DMT\\Ubl\\Generated\\CoreComponentTypeSchemaModuleV2',
        ],
        // sort these scalar-first
        public array $phpTypeMap = [
            'http://www.w3.org/2001/XMLSchema' => [
                'ID' => ['string'],
                'anyURI' => ['string'],
                'base64Binary' => ['string'],
                'boolean' => ['boolean'],
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
                'date' => "DateTime<'Y-m-d'>",
                'time' => "DateTime<'H:i:s'>",
                'dateTime' => "DateTime<'Y-m-d H:i:s'>",
            ],
        ],
        public array $namespaceBlacklist = [
            'http://uri.etsi.org/01903/v1.3.2#',
            'http://uri.etsi.org/01903/v1.4.1#',
            'http://www.w3.org/2000/09/xmldsig#',
            'http://www.w3.org/2009/xmldsig11#',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2',
            'urn:un:unece:uncefact:documentation:2',
        ]
    ) {
    }
}
