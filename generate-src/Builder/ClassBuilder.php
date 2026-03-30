<?php

namespace DMT\Ubl\Generate\Builder;

use DMT\Ubl\Generate\Schema\XsdAttribute;
use DMT\Ubl\Generate\Schema\XsdComplexType;
use DMT\Ubl\Generate\Schema\XsdDocumentation;
use DMT\Ubl\Generate\Schema\XsdElement;
use DMT\Ubl\Generate\Schema\XsdSimpleType;
use InvalidArgumentException;
use Jawira\CaseConverter\Convert;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Until;
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
    public const array SCALAR = ['int', 'float', 'bool', 'string'];

    private BuilderFactory $factory;

    public function __construct(private BuilderConfig $config)
    {
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
        $classNamespace = $this->config->namespace;

        if (isset($this->config->phpNamespaces[$type->namespace])) {
            $classNamespace .= '\\' . $this->config->phpNamespaces[$type->namespace];
        }

        $classBaseName = preg_replace('~Type$~', '', $type->name);

        return "$classNamespace\\$classBaseName";
    }

    public function getAttributePropertyName(XsdAttribute $attribute): string
    {
        $name = (new Convert($attribute->name))->fromPascal()->toCamel();

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

    public function createJMSArrayTypeAttribute(XsdComplexType|XsdSimpleType $type, array &$uses): Attribute
    {
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

    public function createJMSSingleXmlElementAttribute(XsdComplexType|XsdSimpleType $type, array &$uses): Attribute
    {
        $uses[XmlElement::class] = true;
        return $this->factory->attribute(
            'XmlElement',
            [
                'cdata' => false,
                'namespace' => $type->namespace,
            ]
        );
    }

    public function createJMSSerializedNameAttribute($name, array &$uses): Attribute
    {
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
        if ($element->maxOccurs != '1') {
            return $this->createArrayElementProperty($element, $uses);
        } else {
            return $this->createSingleElementProperty($element, $uses);
        }
    }

    public function createSingleElementProperty(XsdElement $element, array &$uses): Property
    {
        $propertyName = $this->getElementPropertyName($element);
        $type = $element->getType();
        $baseType = $this->resolveBaseType($type);
        $nullable = $this->getElementNullable($element);

        $name = $element->name ?? $element->ref;
        if (str_contains($name, ':')) {
            [$ns, $name] = explode(':', $name);
        }

        $prop = $this->factory
            ->property($propertyName)
            ->makePublic()
            ->setType($this->getUnionType($baseType, $nullable, $uses));

        if ($nullable) {
            $prop->setDefault(null);
        }

        $prop->addAttribute($this->createJMSSerializedNameAttribute($name, $uses));
        $prop->addAttribute($this->createJMSSingleTypeAttribute($baseType, $uses));
        $prop->addAttribute($this->createJMSSingleXmlElementAttribute($type, $uses));
        if ($element->since) {
            $uses[Since::class] = true;
            $prop->addAttribute($this->factory->attribute('Since', ['version' => $element->since]));
        }
        if ($element->until) {
            $uses[Until::class] = true;
            $prop->addAttribute($this->factory->attribute('Until', ['version' => $element->until]));
        }

        $documentation = $element->documentation;
        if (!is_null($documentation)) {
            $comment = "/**\n";


            foreach(XsdDocumentation::FIELDS as $field => $property) {
                if (!is_null($documentation->{$property})) {
                    $comment .= sprintf(" * %s: %s\n", $field, $documentation->{$property});
                }
            }

            $comment .= " */";

            $prop->setDocComment($comment);
        }

        return $prop;
    }

    public function createArrayElementProperty(XsdElement $element, array &$uses): Property
    {
        $propertyName = $this->getElementPropertyName($element);
        $type = $element->getType();
        $baseType = $this->resolveBaseType($type);
        $entry = $element->ref ?? $element->name;
        if (str_contains($entry, ':')) {
            [$ns, $entry] = explode(':', $entry);
        }

        $prop = $this->factory
            ->property($propertyName)
            ->makePublic()
            ->setType(new Identifier('array'))
            ->setDefault([]);

        $comment = "/**\n";

        $documentation = $element->documentation;
        if (!is_null($documentation)) {
            foreach(XsdDocumentation::FIELDS as $field => $property) {
                if (!is_null($documentation->{$property})) {
                    $comment .= sprintf(" * %s: %s\n", $field, $documentation->{$property});
                }
            }
        }
        $comment .= sprintf(" * %s\n", $this->createArrayPropertyDocComment($propertyName, $baseType, $uses));
        $comment .= " */";

        $prop->setDocComment($comment);

        $prop->addAttribute($this->createJMSArrayTypeAttribute($baseType, $uses));
        $prop->addAttribute($this->createJMSXmlListAttribute($entry, $type, $uses));

        if ($element->since) {
            $uses[Since::class] = true;
            $prop->addAttribute($this->factory->attribute('Since', ['version' => $element->since]));
        }
        if ($element->until) {
            $uses[Until::class] = true;
            $prop->addAttribute($this->factory->attribute('Until', ['version' => $element->until]));
        }

        return $prop;
    }

    public function createJMSXmlListAttribute(string $entry, XsdComplexType|XsdSimpleType $type, array &$uses): Attribute
    {
        $uses[XmlList::class] = true;

        return $this->factory->attribute(
            'XmlList',
            [
                'entry' => $entry,
                'inline' => true,
                'namespace' => $type->namespace,
            ]
        );
    }

    public function createArrayPropertyDocComment(string $propertyName, XsdComplexType|XsdSimpleType $type, array &$uses): string
    {
        $phpTypes = $this->getPhpTypes($type);
        $phpType = end($phpTypes);

        if (!in_array($phpType, ClassBuilder::SCALAR, true)) {
            $uses[$phpType] = true;
            $phpType = $this->classBaseName($phpType);
        }

        return sprintf(
            '@var array<%s> $%s',
            $phpType,
            $propertyName
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

        if ($type->isRoot()) {
            $class->addAttribute($this->createJMSXmlRootAttribute($type, $uses));

            foreach ($type->schema->namespaces as $prefix => $namespace) {
                $class->addAttribute($this->createJMSXmlNamespaceAttribute($prefix, $namespace, $uses));
            }
        }

        $properties = [];

        $simpleContent = $type->simpleContent ?? null;

        if (!is_null($simpleContent)) {
            $parentType = $simpleContent->getBaseType();
            $baseType = $this->resolveBaseType($parentType);

            $extend = (
                isset($simpleContent->extension) &&
                count($simpleContent->extension->ownAttributes) > 0 &&
                !$baseType instanceof XsdSimpleType
            );

            if ($extend) {
                $baseClassName = $this->getClassName($baseType);
                $uses[$baseClassName] = 'Base';
                $class->extend(new Name('Base'));

                foreach ($simpleContent->extension->ownAttributes as $attribute) {
                    $properties[] = $this->createAttributeProperty($attribute, $uses);
                }
            } else {
                $rootType = $this->resolveRootType($parentType);
                $properties[] = $this->createValueProperty($rootType, $uses);

                if (isset($simpleContent->extension)) {
                    foreach ($simpleContent->extension->attributes as $attribute) {
                        $properties[] = $this->createAttributeProperty($attribute, $uses);
                    }
                } else {
                    foreach ($simpleContent->restriction->attributes as $attribute) {
                        $properties[] = $this->createAttributeProperty($attribute, $uses);
                    }
                }
            }
        }

        foreach ($type->elements as $element) {
            $properties[] = $this->createElementProperty($element, $uses);
        }

        $namespace = $this->factory->namespace($classNamespace);
        $namespaceNode = $namespace->getNode();
        ksort($uses);

        foreach ($uses as $use => $alias) {
            if ($classNamespace == $this->classNamespace($use)) {
                // no need to import things in the same namespace
                continue;
            }

            $useStmt = $this->factory->use($use);

            if (is_string($alias)) {
                $useStmt = $useStmt->as($alias);
            }

            $namespaceNode->stmts[] = $useStmt->getNode();
        }

        $namespaceNode->stmts[] = new Nop();

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
        $comment = "/**\n";
        $comment .= sprintf(" * namespace: %s\n", $type->namespace);
        $comment .= sprintf(" * version: %s\n", $type->schema->version);
        $comment .= sprintf(" * name: %s\n", $type->name);
        $comment .= sprintf(" * path: %s\n", basename($type->schema->path));

        $documentation = $type->documentation;
        if (!is_null($documentation)) {
            foreach(XsdDocumentation::FIELDS as $field => $property) {
                if (!is_null($documentation->{$property})) {
                    $comment .= sprintf(" * %s: %s\n", $field, $documentation->{$property});
                }
            }
        }

        $comment .= " */";

        return $comment;
    }

    private function createValueProperty(XsdComplexType|XsdSimpleType $type, array &$uses): Property {
        $prop = $this->factory
            ->property('value')
            ->makePublic()
            ->setType($this->getUnionType($type, false, $uses));

        $prop->addAttribute($this->createJMSSingleTypeAttribute($type, $uses));
        $prop->addAttribute($this->createJMSXmlValueAttribute($uses));

        return $prop;
    }

    private function createAttributeProperty(XsdAttribute $attribute, array &$uses): Property
    {
        $type = $attribute->getType();
        $baseType = $this->resolveBaseType($type);

        $nullable = $this->getAttributeNullable($attribute);
        $propertyName = $this->getAttributePropertyName($attribute);

        $prop = $this->factory
            ->property($propertyName)
            ->makePublic()
            ->setType($this->getUnionType($baseType, $nullable, $uses));

        if ($nullable) {
            $prop->setDefault(null);
        }

        $documentation = $attribute->documentation;
        if (!is_null($documentation)) {
            $comment = "/**\n";
            foreach(XsdDocumentation::FIELDS as $field => $property) {
                if (!is_null($documentation->{$property})) {
                    $comment .= sprintf(" * %s: %s\n", $field, $documentation->{$property});
                }
            }

            $comment .= " */";

            $prop->setDocComment($comment);
        }

        $prop->addAttribute($this->createJMSXmlAttributeAttribute($uses));
        $prop->addAttribute($this->createJMSSingleTypeAttribute($baseType, $uses));

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

    public function createJMSXmlAttributeAttribute(array &$uses): Attribute
    {
        $uses[XmlAttribute::class] = true;

        return $this->factory->attribute('XmlAttribute');
    }

    public function createJMSXmlValueAttribute(array &$uses): Attribute
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

    public function saveClass(XsdComplexType $type): void
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

    public function getFilename(XsdComplexType $type): string
    {
        $className = $this->getClassName($type);

        if (!str_starts_with($className, $this->config->namespace)) {
            throw new InvalidArgumentException("don't know where to write $className");
        }

        $subNamespace = substr($className, strlen($this->config->namespace));
        $subPath = str_replace('\\', '/', ltrim($subNamespace, '\\'));

        return $this->config->path . '/' . $subPath . '.php';
    }

    public function isNamespaceBlacklisted(string $namespace): bool
    {
        return in_array($namespace, $this->config->namespaceBlacklist);
    }

    public function resolveBaseType(XsdComplexType|XsdSimpleType $type): XsdComplexType|XsdSimpleType
    {
        if ($type instanceof XsdSimpleType) {
            return $type;
        }

        if (count($type->elements) > 0) {
            return $type;
        }

        $baseType = $type->getBaseType();

        if (!in_array($baseType->namespace, $this->config->namespaceBlacklist)) {
            if ($baseType !== $type) {
                return $this->resolveBaseType($baseType);
            }

            return $baseType;
        }

        return $type;
    }

    public function resolveRootType(XsdComplexType|XsdSimpleType $type): XsdComplexType|XsdSimpleType
    {
        if ($type instanceof XsdSimpleType) {
            return $type;
        }

        if (count($type->elements) > 0) {
            return $type;
        }

        return $type->getBaseType();
    }

    public function shouldBuild(XsdComplexType|XsdSimpleType $type): bool
    {
        if ($type instanceof XsdSimpleType) {
            return false;
        }

        if (isset($this->config->phpTypeMap[$type->namespace][$type->name])) {
            return false;
        }

        if (in_array($type->namespace, $this->config->namespaceBlacklist)) {
            return false;
        }

        if (count($type->elements) > 0) {
            return true;
        }

        if (count($type->simpleContent->extension->ownAttributes ?? $type->simpleContent->restriction->ownAttributes ?? []) > 0) {
            return true;
        }

        $baseType = $type->getBaseType();

        if ($baseType instanceof XsdSimpleType) {
            return true;
        }

        if (in_array($baseType->namespace, $this->config->namespaceBlacklist)) {
            return true;
        }

        return false;
    }
}
