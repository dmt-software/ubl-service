<?php

namespace DMT\Test\Ubl\Service\Entity\Components;

use DMT\Ubl\Service\Entity\CommonAggregateComponents\Party;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PartyIdentification;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PartyLegal;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PartyName;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PostalAddress;
use DMT\Ubl\Service\Entity\CommonBasicComponents\EndpointId;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Id;
use DMT\Ubl\Service\Entity\Versions;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class PartyTest extends TestCase
{
    public function testSerializeVersionNlcius(): void
    {
        $context = SerializationContext::create()->setVersion(Versions::VERSION_NLCIUS);

        $party = $this->getParty();

        $xml = simplexml_load_string($this->getSerializer()->serialize($party, 'xml', $context));

        $this->assertEquals('Party', $xml->getName());
        $this->assertEquals(
            strval($party->endpointId),
            strval($xml->xpath('*[local-name()="EndpointID"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="PartyIdentification"]')[0]->getNamespaces()
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="PartyName"]')[0]->getNamespaces()
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="PostalAddress"]')[0]->getNamespaces()
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->getDocNamespaces()
        );
    }

    public function testSerializeVersion10000(): void
    {
        $context = SerializationContext::create()->setVersion(Versions::VERSION_1_0);

        $party = $this->getParty();

        $xml = simplexml_load_string($this->getSerializer()->serialize($party, 'xml', $context));

        $this->assertEquals('Party', $xml->getName());
        $this->assertEmpty($xml->xpath('*[local-name()="EndpointID"]'));
        $this->assertEmpty($xml->xpath('*[local-name()="PartyIdentification"]'));
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="PartyName"]')[0]->getNamespaces()
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="PostalAddress"]')[0]->getNamespaces()
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->getDocNamespaces()
        );
    }

    protected function getParty(): Party
    {
        $party = new Party();
        $party->endpointId = new EndpointId();
        $party->endpointId->id = '1442334659753';
        $party->endpointId->schemeId = '0088';
        $party->partyIdentification = new PartyIdentification();
        $party->partyIdentification->id = new Id();
        $party->partyIdentification->id->id = '12399843';
        $party->partyName = new PartyName();
        $party->partyName->name = 'Holding BV';
        $party->postalAddress = new PostalAddress();
        $party->postalAddress->postalZone = '1234XX';
        $party->partyLegalEntity = new PartyLegal();
        $party->partyLegalEntity->registrationName = 'Holding BV';

        return $party;
    }

    protected function getSerializer(): Serializer
    {
        $builder = SerializerBuilder::create();

        return $builder->build();
    }
}
