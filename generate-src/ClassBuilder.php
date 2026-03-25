<?php

namespace DMT\Ubl\Generate;

use DateTime;
use DMT\Ubl\Generate\Schema\XsdAttribute;
use DMT\Ubl\Generate\Schema\XsdComplexType;
use DMT\Ubl\Generate\Schema\XsdElement;
use DMT\Ubl\Generate\Schema\XsdSchemaCollection;
use DMT\Ubl\Generate\Schema\XsdSimpleContent;
use DMT\Ubl\Generate\Schema\XsdSimpleType;
use DMT\Ubl\Service\Entity\Namespaces;
use Jawira\CaseConverter\Convert;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlList;
use PhpParser\Builder\Property;
use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Nop;
use PhpParser\Node\UnionType;
use PhpParser\PrettyPrinter\Standard;

final class ClassBuilder
{
    private BuilderFactory $factory;

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
        private readonly XsdSchemaCollection $schemaCollection,
    ) {
        $this->factory = new BuilderFactory();
    }

    public function classBaseName(string $className): string
    {
        $parts = explode('\\', $className);

        return end($parts);
    }

    public function classNamespace(string $className): string
    {
        $parts = explode('\\', $className);

        return implode('\\', array_slice($parts, 0, -1));
    }

    public function getClassName(XsdComplexType $type): string
    {
        $classBaseName = preg_replace('~Type$~', '', $type->name);

        $classNamespace = $this->phpNamespaces[$type->namespace] ?? 'DMT\\Ubl\\Service\\Entity';

        return "$classNamespace\\$classBaseName";
    }

    public function getPropertyName(XsdElement $element): string
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

    public function getUnionType(XsdElement $element, bool $nullable, array &$uses): UnionType
    {
        $type = $this->schemaCollection->getElementType($element);

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

    public function getJMSSingleType(XsdElement $element, array &$uses): Expr
    {
        $type = $this->schemaCollection->getElementType($element);

        if (isset($this->jmsTypeMap[$type->name])) {
            return $this->jmsTypeMap[$type->name];
        }

        $className = $this->getPhpType($type);

        $uses[$className] = true;

        return new ClassConstFetch(new Name($this->classBaseName($className)), 'class');
    }

    /**
     * @param XsdComplexType|XsdSimpleType $type
     * @return ?class-string
     */
    public function getPhpType(XsdComplexType|XsdSimpleType $type): ?string
    {
        if (isset($this->phpTypeMap[$type->name])) {
            return $this->phpTypeMap[$type->name];
        }

        return 'blah\\' . $type->name;
    }

    /**
     * @param XsdComplexType|XsdSimpleType $type
     * @return array<string>
     */
    public function getPhpScalarTypes(XsdComplexType|XsdSimpleType $type): array
    {
        $scalarTypes = [];

        if (isset($this->phpScalarTypeMap[$type->name])) {
            foreach ($this->phpScalarTypeMap[$type->name] as $scalarType) {
                $scalarTypes[] = $scalarType;
            }
        }

        return $scalarTypes;
    }

    public function createJMSArrayTypeAttribute(XsdElement $element, array &$uses): Attribute
    {
        $jmsSingleType = $this->getJMSSingleType($element, $uses);

        $stmt = new Concat(new Concat(new String_('array<'), $jmsSingleType), new String_('>'));

        $uses[Type::class] = true;

        return $this->factory->attribute('Type', ['name' => $stmt]);
    }

    public function createJmsSingleTypeAttribute(XsdElement $element, array &$uses): Attribute
    {
        $jmsSingleType = $this->getJMSSingleType($element, $uses);

        $uses[Type::class] = true;
        return $this->factory->attribute(
            'Type',
            ['name' => $jmsSingleType]
        );
    }

    public function createJmsSingleXmlElementAttribute(XsdElement $element, array &$uses): Attribute
    {
        $uses[XmlElement::class] = true;
        $uses[Namespaces::class] = true;

        return $this->factory->attribute(
            'XmlElement',
            [
                'cdata' => false,
                'namespace' => new Arg(new ClassConstFetch(new Name('Namespaces'), strtoupper('NSNSNSNSNS')))
            ]
        );
    }

    public function createJmsSerializedNameAttribute(XsdElement $element, array &$uses): Attribute
    {
        $uses[SerializedName::class] = true;
        return $this->factory->attribute(
            'SerializedName',
            [
                'name' => $element->name
            ]
        );
    }

    public function createElementProperty(XsdElement $element, array &$uses): Property
    {
        if ($element->maxOccurs == 'unbounded') {
            return $this->createUnboundedElementProperty($element, $uses);
        } else {
            return $this->createSingleElementProperty($element, $uses);
        }
    }

    public function createSingleElementProperty(XsdElement $element, array &$uses): Property
    {
        $nullable = $element->minOccurs == '0';

        $prop = $this->factory
            ->property($this->getPropertyName($element))
            ->makePublic()
            ->setType($this->getUnionType($element, $nullable, $uses));

        if ($nullable) {
            $prop->setDefault(null);
        }

        $prop->addAttribute($this->createJmsSerializedNameAttribute($element, $uses));
        $prop->addAttribute($this->createJmsSingleTypeAttribute($element, $uses));
        $prop->addAttribute($this->createJmsSingleXmlElementAttribute($element, $uses));

        return $prop;
    }

    public function createUnboundedElementProperty(XsdElement $element, array &$uses): Property
    {
        $prop = $this->factory
            ->property($this->getPropertyName($element))
            ->makePublic()
            ->setType(new Identifier('array'))
            ->setDefault([]);

        $prop->addAttribute($this->createJMSArrayTypeAttribute($element, $uses));
        $prop->addAttribute($this->createJmsXmlListAttribute($element, $uses));
        $prop->setDocComment($this->createArrayPropertyDocComment($element, $uses));

        return $prop;
    }

    public function createJmsXmlListAttribute(XsdElement $element, array &$uses): Attribute
    {
        $uses[XmlList::class] = true;
        $uses[Namespaces::class] = true;

        return $this->factory->attribute(
            'XmlList',
            [
                'entry' => $element->ref ?? $element->name,
                'inline' => true,
                'namespace' => new Arg(
                    new ClassConstFetch(
                        new Name('Namespaces'), strtoupper('NSNSNSNS')
                    )
                )
            ]
        );
    }

    public function createArrayPropertyDocComment(XsdElement $element, array &$uses): string
    {
        return sprintf(
            '/** @var array<%s> $%s */',
            (new Standard())->prettyPrint([$this->getUnionType($element, false, $uses)]),
            $this->getPropertyName($element)
        );
    }

    public function createClass(XsdComplexType $type): Node
    {
        $uses = [];
        $className = $this->getClassName($type);
        $classBaseName  = $this->classBaseName($className);
        $classNamespace  = $this->classNamespace($className);

        $class = $this->factory->class($classBaseName);
        $class->setDocComment($this->getClassDocComment($type));

        $properties = [];

        $simpleContent = $type->simpleContent;
        if ($simpleContent) {
            $properties[] = $this->createSimpleContentProperty($simpleContent, $uses);

            if ($simpleContent->extension) {
                foreach($simpleContent->extension->attributes as $attribute) {
                    $properties[] = $this->createAttributeProperty($attribute, $uses);
                }
            } else {
                foreach($simpleContent->restriction->attributes as $attribute) {
                    $properties[] = $this->createAttributeProperty($attribute, $uses);
                }
            }
        }

        foreach($this->schemaCollection->getTypeElements($type) as $element) {
            $properties[] = $this->createElementProperty($element, $uses);
        }

        $namespace = $this->factory->namespace($classNamespace);
        $namespaceNode = $namespace->getNode();
        ksort($uses);

        foreach (array_keys($uses) as $use) {
            $namespaceNode->stmts[] = $this->factory->use($use)->getNode();
        }

        $classNode = $class->getNode();

        foreach ($properties as $property) {
            $classNode->stmts[] = $property->getNode();
            $classNode->stmts[] = new Nop();
        }

        $namespaceNode->stmts[] = $classNode;
        $namespaceNode->stmts[] = new Nop();

        return $namespaceNode;
    }

    public function getClassDocComment(XsdComplexType $type): string
    {
        $comment = <<<COMMENT
            /**
             * namespace: %s
             * version: %s
             * id: %s
             * name: %s
             */
        COMMENT;

        return sprintf(
            trim($comment),
            $type->namespace,
            $type->version,
            $type->id,
            $type->name
        );
    }

    private function createSimpleContentProperty(XsdSimpleContent $simpleContent, array &$uses): Property
    {
        return $this->factory->property('content');
    }

    private function createAttributeProperty(XsdAttribute $attribute, array &$uses): Property
    {
        return $this->factory->property($attribute->name);
    }
}
