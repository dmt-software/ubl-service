<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\PartyLegalEntity;
use DMT\Ubl\Service\Entity\Invoice\Type\CompanyId;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class PartyLegalEntityTest extends TestCase
{
    public function testSerialize(): void
    {
        $partyLegalEntity = new PartyLegalEntity();
        $partyLegalEntity->registrationName = 'Holding BV';
        $partyLegalEntity->companyId = new CompanyId();
        $partyLegalEntity->companyId->id = '01000332';

        $xml = simplexml_load_string($this->getSerializer()->serialize($partyLegalEntity, 'xml'));

        $this->assertEquals('PartyLegalEntity', $xml->getName());
        $this->assertEquals(
            $partyLegalEntity->registrationName,
            strval($xml->xpath('*[local-name()="RegistrationName"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="RegistrationName"]')[0]->getNamespaces()
        );
        $this->assertEquals(
            strval($partyLegalEntity->companyId),
            strval($xml->xpath('*[local-name()="CompanyID"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="CompanyID"]')[0]->getNamespaces()
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
