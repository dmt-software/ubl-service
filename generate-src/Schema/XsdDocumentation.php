<?php

namespace DMT\Ubl\Generate\Schema;

use SimpleXMLElement;

final class XsdDocumentation
{
    public const array FIELDS = [
        'AlternativeBusinessTerms' => 'alternativeBusinessTerms',
        'AssociatedObjectClass' => 'associatedObjectClass',
        'Cardinality' => 'cardinality',
        'CategoryCode' => 'categoryCode',
        'Component' => 'component',
        'ComponentType' => 'componentType',
        'DataType' => 'dataType',
        'DataTypeQualifier' => 'dataTypeQualifier',
        'Definition' => 'definition',
        'DictionaryEntryName' => 'dictionaryEntryName',
        'Examples' => 'examples',
        'ObjectClass' => 'objectClass',
        'PrimitiveType' => 'primitiveType',
        'PropertyTerm' => 'propertyTerm',
        'PropertyTermName' => 'propertyTermName',
        'PropertyTermQualifier' => 'propertyTermQualifier',
        'RepresentationTerm' => 'representationTerm',
        'RepresentationTermName' => 'representationTermName',
        'UniqueID' => 'uniqueID',
        'UsageRule' => 'usageRule',
        'VersionID' => 'versionID',
    ];

    public ?string $version;
    public ?string $since;
    public ?string $until;

    public bool $rootElement;

    public ?string $alternativeBusinessTerms;
    public ?string $associatedObjectClass;
    public ?string $cardinality;
    public ?string $categoryCode;
    public ?string $component;
    public ?string $componentType;
    public ?string $dataType;
    public ?string $dataTypeQualifier;
    public ?string $definition;
    public ?string $dictionaryEntryName;
    public ?string $examples;
    public ?string $objectClass;
    public ?string $primitiveType;
    public ?string $propertyTerm;
    public ?string $propertyTermName;
    public ?string $propertyTermQualifier;
    public ?string $representationTerm;
    public ?string $representationTermName;
    public ?string $uniqueID;
    public ?string $usageRule;
    public ?string $versionID;

    private function __construct(public XsdSchema $schema)
    {
    }

    public static function fromXml(XsdSchema $schema, SimpleXMLElement $xml): XsdDocumentation
    {
        $instance = new XsdDocumentation($schema);
        $instance->version = $schema->version;
        $instance->since = $schema->version;
        $instance->until = $schema->version;

        foreach (XsdDocumentation::FIELDS as $field => $property) {
            $value = $xml->xpath("*[local-name()='{$field}']")[0] ?? null;
            $instance->{lcFirst($field)} = $value;
        }

        $instance->rootElement = str_contains((string)$xml, 'root element');

        return $instance;
    }
}
