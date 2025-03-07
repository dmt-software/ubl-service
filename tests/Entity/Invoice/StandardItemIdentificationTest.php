<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\StandardItemIdentification;
use DMT\Ubl\Service\Entity\Invoice\Type\Id;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class StandardItemIdentificationTest extends TestCase
{
    public function testSerialize(): void
    {
        $standardItemIdentification = new StandardItemIdentification();
        $standardItemIdentification->id = new Id();
        $standardItemIdentification->id->id = '1234567899992';

        $xml = simplexml_load_string($this->getSerializer()->serialize($standardItemIdentification, 'xml'));

        $this->assertEquals('StandardItemIdentification', $xml->getName());
        $this->assertEquals(
            strval($standardItemIdentification->id),
            strval($xml->xpath('*[local-name()="ID"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ID"]')[0]->getNamespaces()
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->getDocNamespaces()
        );
    }

    public function getSerializer(): Serializer
    {
        $builder = SerializerBuilder::create();

        return $builder->build();
    }
}
