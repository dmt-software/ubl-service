<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\PartyName;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class PartyNameTest extends TestCase
{
    public function testSerialize(): void
    {
        $partyName = new PartyName();
        $partyName->name = 'Party Name';

        $xml = simplexml_load_string($this->getSerializer()->serialize($partyName, 'xml'));

        $this->assertEquals('PartyName', $xml->getName());
        $this->assertEquals(
            $partyName->name,
            strval($xml->xpath('*[local-name()="Name"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="Name"]')[0]->getNamespaces()
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
