<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\PartyIdentification;
use DMT\Ubl\Service\Entity\Invoice\Type\Id;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class PartyIdentificationTest extends TestCase
{
    public function testSerialize(): void
    {
        $partyIdentification = new PartyIdentification();
        $partyIdentification->id = new Id();
        $partyIdentification->id->id = '2233166543987';

        $xml = simplexml_load_string($this->getSerializer()->serialize($partyIdentification, 'xml'));

        $this->assertEquals('PartyIdentification', $xml->getName());
        $this->assertEquals(
            strval($partyIdentification->id),
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
