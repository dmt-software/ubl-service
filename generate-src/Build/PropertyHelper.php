<?php

namespace DMT\Ubl\Generate\Build;

use DateTime;
use DMT\Ubl\Generate\Schema\ComplexType;
use DMT\Ubl\Generate\Schema\Element;
use DMT\Ubl\Generate\Schema\Environment;
use DMT\Ubl\Generate\Schema\SimpleType;
use InvalidArgumentException;
use Jawira\CaseConverter\Convert;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\UnionType;
use PhpParser\PrettyPrinter\Standard;
use ReflectionClass;
use ReflectionException;

class PropertyHelper
{
    private array $propertyNameReplace = [
        '~^ubl~i' => 'ubl',
        '~id$~i' => 'Id',
        '~^uuid$~i' => 'uuid',
        '~^Id$~' => 'id',
    ];

    private array $phpScalarTypeMap = [
        'Code' => ['string'],
        'Identifier' => ['string'],
        'Indicator' => ['bool'],
        'Text' => ['string'],
    ];

    private array $phpTypeMap = [
        'Date' => DateTime::class,
        'Time' => DateTime::class,
    ];

    public function __construct(
        private Environment $environment,
    ) {
    }

    public function classBaseName(string $class): string
    {
        $parts = explode('\\', $class);
        return end($parts);
    }

    public function getPropertyName(Element $element): string
    {
        if (!is_null($element->ref)) {
            if (str_contains($element->ref, ':')) {
                [$ns, $name] = explode(':', $element->ref);
            } else {
                $name = $element->ref;
            }

        } else {
            $name = $element->name;
        }

        $name = (new Convert($name))->fromPascal()->toCamel();

        return preg_replace(
            array_keys($this->propertyNameReplace),
            array_values($this->propertyNameReplace),
            $name
        );
    }

    public function getType(Element $element): ComplexType|SimpleType
    {
        $ref = $element->ref ?? $element->type;

        if (!str_contains($ref, ':')) {
            // must be in the same namespace
            return $element->schema->types[$ref];
        }

        [$ns, $type] = explode(':', $ref);


        if ($ns == 'xsd') {
            return new SimpleType($type);
        }

        $ns = $element->schema->namespaces[$ns] ?? $ns;

        return $this->environment->getType($ns, $type);
    }

    public function getUnionType(Element $element, bool $nullable, array &$uses): UnionType
    {
        $type = $this->getType($element);

        $types = [];

        if ($nullable) {
            $types[] = new Identifier('null');
        }

        foreach ($this->getPhpScalarTypes($type) as $scalarType) {
            $types[] = new Identifier($scalarType);
        }

        $className = $this->getPhpType($type);
        $uses[$className] = true;
        $types[] = new Name($this->classBaseName($className));

        return new UnionType($types);
    }

    public function getJMSSingleType(Element $element, array &$uses): string
    {
        $type = $this->getType($element);

        if (isset($this->jmsTypeMap[$type->name])) {
            return $this->jmsTypeMap[$type->name];
        }

        $className = $this->getPhpType($type);

        $uses[$className] = true;

        $stmt = new ClassConstFetch(new Name($this->classBaseName($className)), 'class');

        return (new Standard())->prettyPrint([$stmt]);
    }

    public function getJMSArrayType(Element $element, array &$uses): string
    {
        $jmsSingleType = $this->getJMSSingleType($element, $uses);

        $stmt = new Concat(new Concat(new String_('array<'), $jmsSingleType), new String_('>'));

        return (new Standard())->prettyPrint([$stmt]);
    }

    /**
     * @param ComplexType|SimpleType $type
     * @return ?class-string
     */
    public function getPhpType(ComplexType|SimpleType $type): ?string
    {
        if (isset($this->phpTypeMap[$type->name])) {
            return $this->phpTypeMap[$type->name];
        }

        var_dump($type->schema);
        var_dump($type);

        return 'blah\\' . $type->name;
    }

    /**
     * @param ComplexType|SimpleType $type
     * @return array<string>
     */
    public function getPhpScalarTypes(ComplexType|SimpleType $type): array
    {
        $scalarTypes = [];

        if (isset($this->phpScalarTypeMap[$type->name])) {
            foreach($this->phpScalarTypeMap[$type->name] as $scalarType) {
                $scalarTypes[] = $scalarType;
            }
        }

        return $scalarTypes;
    }
}
