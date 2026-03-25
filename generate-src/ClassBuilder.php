<?php

namespace DMT\Ubl\Generate;

use DMT\Ubl\Generate\Schema\XsdAttribute;
use DMT\Ubl\Generate\Schema\XsdComplexType;
use DMT\Ubl\Generate\Schema\XsdElement;
use DMT\Ubl\Generate\Schema\XsdSchemaCollection;
use DMT\Ubl\Generate\Schema\XsdSimpleContent;
use DMT\Ubl\Generate\Schema\XsdSimpleType;
use InvalidArgumentException;
use Jawira\CaseConverter\Convert;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlNamespace;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlValue;
use PhpParser\Builder\Property;
use PhpParser\BuilderFactory;
use PhpParser\Node;
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

final readonly class ClassBuilder
{
    public const array SCALAR = ['int', 'float', 'boolean', 'string'];

    private BuilderFactory $factory;

    public function __construct(
        private XsdSchemaCollection $schemaCollection,
        private BuilderConfig $config,
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

        $classNamespace = $this->config->phpNamespaces[$type->namespace] ?? $this->config->namespace;

        return "$classNamespace\\$classBaseName";
    }

    public function getAttributePropertyName(XsdAttribute $attribute): string
    {
        $name = $attribute->name;

        $name = (new Convert($name))->fromPascal()->toCamel();

        return preg_replace(
            array_keys($this->config->propertyNameReplace),
            array_values($this->config->propertyNameReplace),
            $name
        );
    }

    public function getElementPropertyName(XsdElement $element): string
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
            array_keys($this->config->propertyNameReplace),
            array_values($this->config->propertyNameReplace),
            $name
        );
    }

    public function getUnionType(XsdComplexType|XsdSimpleType $type, bool $nullable, array &$uses): UnionType
    {
        $unionTypes = [];

        if ($nullable) {
            $unionTypes[] = new Identifier('null');
        }

        foreach ($this->getPhpTypes($type) as $phpType) {
            if (in_array($phpType, ClassBuilder::SCALAR, true)) {
                $unionTypes[] = new Identifier($phpType);
            } else {
                $uses[$phpType] = true;
                $unionTypes[] = new Name($this->classBaseName($phpType));
            }
        }

        if ($nullable && count($unionTypes) == 1 || count($unionTypes) == 0) {
            var_dump($type);
            throw new InvalidArgumentException("unmapped types found");
        }

        return new UnionType($unionTypes);
    }

    public function getAttributeUnionType(XsdAttribute $attribute, array &$uses): UnionType
    {
        return $this->getUnionType(
            $this->schemaCollection->getAttributeType($attribute),
            $this->getAttributeNullable($attribute),
            $uses
        );
    }

    public function getElementUnionType(XsdElement $element, array &$uses): UnionType
    {
        return $this->getUnionType(
            $this->schemaCollection->getElementType($element),
            $this->getElementNullable($element),
            $uses
        );
    }

    public function getJMSSingleType(XsdComplexType|XsdSimpleType $type, array &$uses): Expr
    {
        if (isset($this->config->jmsTypeMap[$type->namespace][$type->name])) {
            return new String_($this->config->jmsTypeMap[$type->namespace][$type->name]);
        }

        $phpTypes = $this->getPhpTypes($type);
        $phpType = end($phpTypes);

        if (in_array($phpType, ClassBuilder::SCALAR, true)) {
            return new String_($phpType);
        }

        $uses[$phpType] = true;

        return new ClassConstFetch(new Name($this->classBaseName($phpType)), 'class');
    }

    /**
     * This list is sorted like this: null, other scalar types, classes
     * @param XsdComplexType|XsdSimpleType $type
     * @return array<class-string>
     */
    public function getPhpTypes(XsdComplexType|XsdSimpleType $type): array
    {
        if (isset($this->config->phpTypeMap[$type->namespace][$type->name])) {
            return $this->config->phpTypeMap[$type->namespace][$type->name];
        }

        if ($type instanceof XsdSimpleType) {
            print_r($type);

            throw new InvalidArgumentException("unmapped types found");
        }

        return [$this->getClassName($type)];
    }

    public function createJMSArrayTypeAttribute(XsdElement $element, array &$uses): Attribute
    {
        $type = $this->schemaCollection->getElementType($element);

        $jmsSingleType = $this->getJMSSingleType($type, $uses);

        $stmt = new Concat(new Concat(new String_('array<'), $jmsSingleType), new String_('>'));

        $uses[Type::class] = true;

        return $this->factory->attribute('Type', ['name' => $stmt]);
    }

    public function createJMSSingleTypeAttribute(XsdComplexType|XsdSimpleType $type, array &$uses): Attribute
    {
        $jmsSingleType = $this->getJMSSingleType($type, $uses);

        $uses[Type::class] = true;
        return $this->factory->attribute(
            'Type',
            ['name' => $jmsSingleType]
        );
    }

    public function createJMSSingleXmlElementAttribute(XsdElement $element, array &$uses): Attribute
    {
        $uses[XmlElement::class] = true;

        return $this->factory->attribute(
            'XmlElement',
            [
                'cdata' => false,
                'namespace' => $element->namespace,
            ]
        );
    }

    public function createJMSSerializedNameAttribute(XsdElement $element, array &$uses): Attribute
    {
        $name = $element->name ?? $element->ref;
        if (str_contains($name, ':')) {
            [$ns, $name] = explode(':', $name);
        }

        $uses[SerializedName::class] = true;
        return $this->factory->attribute(
            'SerializedName',
            [
                'name' => $name,
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
        $type = $this->schemaCollection->getElementType($element);

        $prop = $this->factory
            ->property($this->getElementPropertyName($element))
            ->makePublic()
            ->setType($this->getElementUnionType($element, $uses));

        if ($this->getElementNullable($element)) {
            $prop->setDefault(null);
        }

        $prop->addAttribute($this->createJMSSerializedNameAttribute($element, $uses));
        $prop->addAttribute($this->createJMSSingleTypeAttribute($type, $uses));
        $prop->addAttribute($this->createJMSSingleXmlElementAttribute($element, $uses));

        return $prop;
    }

    public function createUnboundedElementProperty(XsdElement $element, array &$uses): Property
    {
        $prop = $this->factory
            ->property($this->getElementPropertyName($element))
            ->makePublic()
            ->setType(new Identifier('array'))
            ->setDefault([]);

        $prop->setDocComment($this->createArrayPropertyDocComment($element, $uses));
        $prop->addAttribute($this->createJMSArrayTypeAttribute($element, $uses));
        $prop->addAttribute($this->createJMSXmlListAttribute($element, $uses));

        return $prop;
    }

    public function createJMSXmlListAttribute(XsdElement $element, array &$uses): Attribute
    {
        $entry = $element->ref ?? $element->name;
        if (str_contains($entry, ':')) {
            [$ns, $entry] = explode(':', $entry);
        }

        $uses[XmlList::class] = true;

        return $this->factory->attribute(
            'XmlList',
            [
                'entry' => $entry,
                'inline' => true,
                'namespace' => $element->namespace,
            ]
        );
    }

    public function createArrayPropertyDocComment(XsdElement $element, array &$uses): string
    {
        $type = $this->schemaCollection->getElementType($element);
        $phpTypes = $this->getPhpTypes($type);
        $phpType = end($phpTypes);

        if (!in_array($phpType, ClassBuilder::SCALAR, true)) {
            $uses[$phpType] = true;
            $phpType = $this->classBaseName($phpType);
        }

        return sprintf(
            '/** @var array<%s> $%s */',
            $phpType,
            $this->getElementPropertyName($element)
        );
    }

    public function createClass(XsdComplexType $type): Node
    {
        $uses = [];
        $className = $this->getClassName($type);
        $classBaseName = $this->classBaseName($className);
        $classNamespace = $this->classNamespace($className);

        $class = $this->factory->class($classBaseName);
        $class->setDocComment($this->getClassDocComment($type));
        $class->addAttribute($this->createJMSXmlRootAttribute($type, $uses));

        foreach ($type->namespaces as $prefix => $namespace) {
            if ($this->isNamespaceBlacklisted($namespace)) {
                continue;
            }

            $class->addAttribute($this->createJMSXmlNamespaceAttribute($prefix, $namespace, $uses));
        }

        $properties = [];

        $simpleContent = $type->simpleContent;
        if ($simpleContent && !$this->isNamespaceBlacklisted($simpleContent->namespace)) {
            $properties[] = $this->createSimpleContentProperty($className, $simpleContent, $uses);

            if ($simpleContent->extension) {
                foreach ($simpleContent->extension->attributes as $attribute) {
                    $properties[] = $this->createAttributeProperty($attribute, $uses);
                }
            } else {
                foreach ($simpleContent->restriction->attributes as $attribute) {
                    $properties[] = $this->createAttributeProperty($attribute, $uses);
                }
            }
        }

        foreach ($this->schemaCollection->getTypeElements($type) as $element) {
            if ($this->isNamespaceBlacklisted($element->namespace)) {
                continue;
            }

            $properties[] = $this->createElementProperty($element, $uses);
        }

        $namespace = $this->factory->namespace($classNamespace);
        $namespaceNode = $namespace->getNode();
        ksort($uses);

        foreach (array_keys($uses) as $use) {
            if ($classNamespace == $this->classNamespace($use)) {
                // no need to import things in the same namespace
                continue;
            }

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

    private function createSimpleContentProperty(
        string $className,
        XsdSimpleContent $simpleContent,
        array &$uses
    ): Property {
        $type = $this->schemaCollection->getSimpleContentType($simpleContent);

        $prop = $this->factory
            ->property(lcfirst($this->classBaseName($className)))
            ->makePublic()
            ->setType($this->getUnionType($type, false, $uses));

        $prop->addAttribute($this->createJMSSingleTypeAttribute($type, $uses));
        $prop->addAttribute($this->createJMSXmlValueAttribute($simpleContent, $uses));

        return $prop;
    }

    private function createAttributeProperty(XsdAttribute $attribute, array &$uses): Property
    {
        $prop = $this->factory
            ->property($this->getAttributePropertyName($attribute))
            ->makePublic()
            ->setType($this->getAttributeUnionType($attribute, $uses));

        if ($this->getAttributeNullable($attribute)) {
            $prop->setDefault(null);
        }

        $type = $this->schemaCollection->getAttributeType($attribute);

        $prop->addAttribute($this->createJMSXmlAttributeAttribute($attribute, $uses));
        $prop->addAttribute($this->createJMSSingleTypeAttribute($type, $uses));

        return $prop;
    }

    public function getAttributeNullable(XsdAttribute $attribute): bool
    {
        return $attribute->use == 'optional';
    }

    public function getElementNullable(XsdElement $element): bool
    {
        return $element->minOccurs == '0';
    }

    public function createJMSXmlAttributeAttribute(XsdAttribute $attribute, array &$uses): Attribute
    {
        $uses[XmlAttribute::class] = true;

        return $this->factory->attribute('XmlAttribute');
    }

    public function createJMSXmlValueAttribute(XsdSimpleContent $simpleContent, array &$uses): Attribute
    {
        $uses[XmlValue::class] = true;

        return $this->factory->attribute('XmlValue');
    }

    private function createJMSXmlRootAttribute(XsdComplexType $type, array &$uses): Attribute
    {
        $uses[XmlRoot::class] = true;

        $name = $type->name;
        if (str_contains($name, ':')) {
            [$ns, $name] = explode(':', $name);
        }

        return $this->factory->attribute(
            'XmlRoot',
            [
                'name' => $name,
                'namespace' => $type->namespace,
            ]
        );
    }

    private function createJMSXmlNamespaceAttribute(string $prefix, string $uri, array &$uses): Attribute
    {
        $uses[XmlNamespace::class] = true;

        return $this->factory->attribute(
            'XmlNamespace',
            [
                'uri' => $uri,
                'prefix' => $prefix,
            ]
        );
    }

    public function saveClass(XsdComplexType|XsdSimpleType $type): void
    {
        $stmt = $this->createClass($type);

        $printer = new Standard();
        $contents = $printer->prettyPrintFile([$stmt]);
        $filename = $this->getFilename($type);

        $dir = dirname($filename);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($filename, $contents);
    }

    private function getFilename(XsdComplexType $type): string
    {
        $className = $this->getClassName($type);

        if (!str_starts_with($className, $this->config->namespace)) {
            throw new InvalidArgumentException("don't know where to write $className");
        }

        $subNamespace = substr($className, strlen($this->config->namespace));
        $subPath = str_replace('\\','/', ltrim($subNamespace, '\\'));

        return $this->config->path . '/' . $subPath . '.php';
    }

    public function isNamespaceBlacklisted(string $namespace): bool
    {
        return in_array($namespace, $this->config->namespaceBlacklist);
    }
}
