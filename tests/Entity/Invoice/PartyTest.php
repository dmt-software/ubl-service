<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Party;
use DMT\Ubl\Service\Entity\Invoice\PartyIdentification;
use DMT\Ubl\Service\Entity\Invoice\PartyLegalEntity;
use DMT\Ubl\Service\Entity\Invoice\PartyName;
use DMT\Ubl\Service\Entity\Invoice\PostalAddress;
use DMT\Ubl\Service\Entity\Invoice\Type\EndpointId;
use DMT\Ubl\Service\Entity\Invoice\Type\Id;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class PartyTest extends TestCase
{
    public function testVersion20000(): void
    {
        $context = SerializationContext::create()->setVersion('2.0');

        $party = $this->getParty();

        $xml = simplexml_load_string($this->getSerializer()->serialize($party, 'xml', $context));

        $this->assertEquals($party->endpointId->id, $xml->xpath('*[local-name()="EndpointID"]')[0]);
        $this->assertEquals(
            strval($party->partyIdentification->id),
            $xml->xpath('*[local-name()="PartyIdentification"]/*[local-name()="ID"]')[0]
        );
        $this->assertEquals(
            $party->partyName->name,
            $xml->xpath('*[local-name()="PartyName"]/*[local-name()="Name"]')[0]
        );
        $this->assertEquals(
            $party->postalAddress->streetName,
            $xml->xpath('*[local-name()="PostalAddress"]/*[local-name()="StreetName"]')[0]
        );
        $this->assertEquals(
            $party->postalAddress->cityName,
            $xml->xpath('*[local-name()="PostalAddress"]/*[local-name()="CityName"]')[0]
        );
        $this->assertEquals(
            $party->postalAddress->postalZone,
            $xml->xpath('*[local-name()="PostalAddress"]/*[local-name()="PostalZone"]')[0]
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->getDocNamespaces()
        );
    }

    public function testVersion10000(): void
    {
        $context = SerializationContext::create()->setVersion('1.0');

        $party = $this->getParty();

        $xml = simplexml_load_string($this->getSerializer()->serialize($party, 'xml', $context));

        $this->assertEmpty($xml->xpath('*[local-name()="EndpointID"]'));
        $this->assertEmpty($xml->xpath('*[local-name()="PartyIdentification"]'));
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
        $party->partyIdentification->id->schemeId = 'NL:KVK';
        $party->partyName = new PartyName();
        $party->partyName->name = 'Holding BV';
        $party->postalAddress = new PostalAddress();
        $party->postalAddress->streetName = 'Viewstreet';
        $party->postalAddress->postalZone = '1234XX';
        $party->postalAddress->cityName = 'Municipality';
        $party->partyLegalEntity = new PartyLegalEntity();
        $party->partyLegalEntity->registrationName = 'Holding BV';

        return $party;
    }

    protected function getSerializer(): Serializer
    {
        $builder = SerializerBuilder::create();

        return $builder->build();
    }
}
