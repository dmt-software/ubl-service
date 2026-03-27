<?php

namespace DMT\Ubl\Service\Handler;

use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\XmlDeserializationVisitor;
use SimpleXMLElement;

final class RawXmlHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods(): array
    {
        return [
            [
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'xml',
                'type' => 'RawXml', // Your custom type alias
                'method' => 'deserializeRawXml',
            ],
        ];
    }

    public function deserializeRawXml(XmlDeserializationVisitor $visitor, SimpleXMLElement $data, array $type): SimpleXMLElement
    {
        return $data;
    }
}