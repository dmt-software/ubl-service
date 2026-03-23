<?php

namespace DMT\Ubl\Generate;

use DMT\Ubl\Service\Entity\Namespaces;
use InvalidArgumentException;
use Jawira\CaseConverter\Convert;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlList;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\UnionType;
use PhpParser\PrettyPrinter\Standard;
use SimpleXMLElement;

class ComponentSchema
{
    private array $ignoreElements = [
        'UBLExtensions',
        'ProfileExecutionID',
    ];

    private array $propertyNameReplace = [
        '~^ubl~i' => 'ubl',
        '~id$~i' => 'Id',
        '~^uuid$~i' => 'uuid',
        '~^Id$~' => 'id',
    ];

    private array $jmsTypeMap = [
        'Date' => 'DateTime<\'Y-m-d\'>',
        'Time' => 'DateTime<\'H:i:s\'>',
    ];

    private array $phpTypeMap = [
        'Date' => 'DateTime',
        'Time' => 'DateTime',
    ];

    private array $phpSimpleTypeMap = [
        'Code' => ['string'],
        'Identifier' => ['string'],
        'Indicator' => ['bool'],
        'Text' => ['string'],
    ];

    public function __construct(
        public string $ns,
        public SimpleXMLElement $element,
        public SimpleXMLElement $complexType,
    ) {
    }

    public function getType(): string
    {
        $type = (string)$this->element->attributes()->type;

        if ($this->ns == 'cac') {
            if (!str_ends_with($type, 'Type')) {
                throw new InvalidArgumentException("don't know what to do with $type");
            }

            return substr($type, 0, -strlen('Type'));
        }

        if ($this->ns == 'cbc') {
            $extension = $this->complexType->xpath('.//xsd:extension')[0];
            $base = (string)$extension->attributes()->base;

            [$ns, $baseType] = explode(':', $base);

            if (!str_ends_with($baseType, 'Type')) {
                throw new InvalidArgumentException("don't know what to do with $type / $ns:$baseType");
            }

            return substr($baseType, 0, -strlen('Type'));
        }

        throw new InvalidArgumentException("don't know what to do with $type");
    }

    public function getElementName(): string
    {
        return $this->element->attributes()->name;
    }

    public function getPropertyName(): string
    {
        $name = (string)$this->element->attributes()['name'];
        $name = (new Convert($name))->fromPascal()->toCamel();

        return preg_replace(
            array_keys($this->propertyNameReplace),
            array_values($this->propertyNameReplace),
            $name
        );
    }

    public function isIgnored(): bool
    {
        return in_array($this->getElementName(), $this->ignoreElements, true);
    }

    public function build(BuildContext $ctx, bool $nullable, bool $array): void
    {
        if ($array) {
            $this->buildArrayProperty($ctx);
        } else {
            $this->buildSingleProperty($ctx, $nullable);
        }
    }

    private function buildSingleProperty(BuildContext $ctx, bool $nullable): void
    {
        $prop = $ctx
            ->factory
            ->property($this->getPropertyName())
            ->makePublic()
            ->setType($this->getUnionType($ctx, $nullable));

        if ($nullable) {
            $prop->setDefault(null);
        }

        $ctx->uses[SerializedName::class] = true;
        $prop->addAttribute(
            $ctx->factory->attribute(
                'SerializedName',
                [
                    'name' => $this->getElementName()
                ]
            )
        );

        $ctx->uses[Type::class] = true;
        $prop->addAttribute(
            $ctx->factory->attribute(
                'Type',
                [
                    'name' => $this->getJMSType(false)
                ]
            )
        );

        $ctx->uses[XmlElement::class] = true;
        $ctx->uses[Namespaces::class] = true;
        $prop->addAttribute(
            $ctx->factory->attribute(
                'XmlElement',
                [
                    'cdata' => false,
                    'namespace' => new Arg(
                        new ClassConstFetch(
                            new Name('Namespaces'), strtoupper($this->ns)
                        )
                    )
                ]
            )
        );

        $ctx->properties[] = $prop;
    }

    private function buildArrayProperty(BuildContext $ctx): void
    {
        $prop = $ctx
            ->factory
            ->property($this->getPropertyName())
            ->makePublic()
            ->setType(new Identifier('array'))
            ->setDefault([]);

        $ctx->uses[Type::class] = true;
        $prop->addAttribute(
            $ctx->factory->attribute(
                'Type',
                [
                    'name' => $this->getJMSType(true)
                ]
            )
        );

        $ctx->uses[XmlList::class] = true;
        $ctx->uses[Namespaces::class] = true;
        $prop->addAttribute(
            $ctx->factory->attribute(
                'XmlList',
                [
                    'entry' => $this->getElementName(),
                    'inline' => true,
                    'namespace' => new Arg(
                        new ClassConstFetch(
                            new Name('Namespaces'), strtoupper($this->ns)
                        )
                    )
                ]
            )
        );

        // @todo: docblock
        $prop->setDocComment(
            sprintf(
                '/** @var array<%s> $%s */',
                (new Standard())->prettyPrint([$this->getUnionType($ctx, false)]    ),
                $this->getPropertyName()
            )
        );

        $ctx->properties[] = $prop;

//        <<<END
//        #[Type(name: 'array<' . $phpType::class . '>')]
//        #[XmlList(entry: "$elementName", inline: true, namespace: Namespaces::$nsUpper)]
//
//        public array \$$propertyName = [];
//
//        END;
    }

    private function getUnionType(BuildContext $ctx, bool $nullable): UnionType
    {
        $type = $this->getType();

        $types = [];

        if ($nullable) {
            $types[] = new Identifier('null');
        }

        if (isset($this->phpSimpleTypeMap[$type])) {
            foreach($this->phpSimpleTypeMap[$type] as $simpleType) {
                $types[] = new Identifier($simpleType);
            }
        }

        if (isset($this->phpTypeMap[$type])) {
            $ctx->uses[$this->phpTypeMap[$type]] = true;
            $types[] = new Name($this->phpTypeMap[$type]);
        } else {
            $ctx->uses[$ctx->namespaces[$this->ns] . $type] = true;
            $types[] = new Name($type);
        }

        return new UnionType($types);
    }

    public function getJMSType(bool $array): string|Concat|ClassConstFetch
    {
        $type = $this->getType();

        if (isset($this->jmsTypeMap[$type])) {
            return $this->jmsTypeMap[$type];
        }

        $classConst = new ClassConstFetch(new Name($type), 'class');

        if (!$array) {
            return $classConst;
        }

        return new Concat(
            new Concat(
                new String_('array<'),
                $classConst
            ),
            new String_('>')
        );
    }
}