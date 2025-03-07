<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\AccountingSupplierParty;
use DMT\Ubl\Service\Entity\Invoice\Party;
use DMT\Ubl\Service\Entity\Invoice\PartyName;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class AccountingSupplierPartyTest extends TestCase
{
    public function testSerialize(): void
    {
        $party = new AccountingSupplierParty();
        $party->party = new Party();
        $party->party->partyName = new PartyName();
        $party->party->partyName->name = 'Company';

        $xml = simplexml_load_string($this->getSerializer()->serialize($party, 'xml'));

        $this->assertEquals('AccountingSupplierParty', $xml->getName());
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="Party"]')[0]->getNamespaces()
        );
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->getDocNamespaces()
        );
    }

    protected function getSerializer(): Serializer
    {
        $builder = SerializerBuilder::create();

        return $builder->build();
    }
}
