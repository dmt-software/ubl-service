<?php

namespace DMT\Ubl\Service\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\NonVisitableTypeException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use SimpleXMLElement;

final class UnionHandler implements SubscribingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods(): iterable
    {
        $methods = [];
        $formats = ['json', 'xml'];

        foreach ($formats as $format) {
            $methods[] = [
                'type' => 'union',
                'format' => $format,
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'method' => 'deserializeUnion',
            ];
            $methods[] = [
                'type' => 'union',
                'format' => $format,
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'method' => 'serializeUnion',
            ];
        }

        return $methods;
    }

    public function serializeUnion(
        SerializationVisitorInterface $visitor,
        mixed $data,
        array $type,
        SerializationContext $context
    ): mixed {
        if ($this->isPrimitiveType(gettype($data))) {
            return $this->matchSimpleType($data, $type, $context);
        } else {
            $resolvedType = [
                'name' => get_class($data),
                'params' => [],
            ];

            return $context->getNavigator()->accept($data, $resolvedType);
        }
    }

    public function deserializeUnion(DeserializationVisitorInterface $visitor, mixed $data, array $type, DeserializationContext $context): mixed
    {
        if ($data instanceof SimpleXMLElement) {
            $types = array_filter(
                array_merge(...$type['params']),
                fn(array $possibleType) => $this->isPrimitiveType($possibleType['name'])
            );

            if (!$types) {
                throw new RuntimeException('XML deserialisation into union types without scalar type is not supported yet.');
            }

            return $context->getNavigator()->accept($data, current($types));
        }

        if (3 === count($type['params'])) {
            $lookupField = $type['params'][1];
            if (empty($data[$lookupField])) {
                throw new NonVisitableTypeException(sprintf('Union Discriminator Field "%s" not found in data', $lookupField));
            }

            $unionMap = $type['params'][2];
            $lookupValue = $data[$lookupField];
            if (empty($unionMap[$lookupValue])) {
                throw new NonVisitableTypeException(sprintf('Union Discriminator Map does not contain key "%s"', $lookupValue));
            }

            $finalType = [
                'name' => $unionMap[$lookupValue],
                'params' => [],
            ];

            return $context->getNavigator()->accept($data, $finalType);
        }

        foreach ($type['params'][0] as $possibleType) {
            if ($this->isPrimitiveType($possibleType['name']) && $this->testPrimitive($data, $possibleType['name'], $context->getFormat())) {
                return $context->getNavigator()->accept($data, $possibleType);
            }
        }

        return null;
    }

    private function matchSimpleType(mixed $data, array $type, Context $context): mixed
    {
        foreach ($type['params'][0] as $possibleType) {
            if ($this->isPrimitiveType($possibleType['name']) && !$this->testPrimitive($data, $possibleType['name'], $context->getFormat())) {
                continue;
            }

            try {
                return $context->getNavigator()->accept($data, $possibleType);
            } catch (NonVisitableTypeException $e) {
                continue;
            }
        }

        return null;
    }

    private function isPrimitiveType(string $type): bool
    {
        return in_array($type, ['int', 'integer', 'float', 'double', 'bool', 'boolean', 'true', 'false', 'string', 'array'], true);
    }

    private function testPrimitive(mixed $data, string $type, string $format): bool
    {
        switch ($type) {
            case 'array':
                return is_array($data);

            case 'integer':
            case 'int':
                return (string) (int) $data === (string) $data;

            case 'double':
            case 'float':
                return (string) (float) $data === (string) $data;

            case 'bool':
            case 'boolean':
                return (string) (bool) $data === (string) $data;

            case 'true':
                return true === $data;

            case 'false':
                return false === $data;

            case 'string':
                return is_string($data);
        }

        return false;
    }
}
