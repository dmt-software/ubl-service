<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Contact;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class ContactTest extends TestCase
{
    public function testSerialize(): void
    {
        $contact = new Contact();
        $contact->name = 'John Doe';
        $contact->electronicMail = 'john@doe.com';
        $contact->telephone = '0123456789';

        $xml = simplexml_load_string($this->getSerializer()->serialize($contact, 'xml'));

        $this->assertEquals('Contact', $xml->getName());
        $this->assertEquals(
            $contact->name,
            strval($xml->xpath('*[local-name()="Name"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="Name"]')[0]->getNamespaces()
        );
        $this->assertEquals(
            $contact->electronicMail,
            strval($xml->xpath('*[local-name()="ElectronicMail"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="ElectronicMail"]')[0]->getNamespaces()
        );
        $this->assertEquals(
            $contact->telephone,
            strval($xml->xpath('*[local-name()="Telephone"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="Telephone"]')[0]->getNamespaces()
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
