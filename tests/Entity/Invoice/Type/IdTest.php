<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice\Type\Id;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class IdTest extends TestCase
{
    public function testVersion20000(): void
    {
        $context = SerializationContext::create()->setVersion('2.0');

        $id = new Id();
        $id->id = '1442334659753';
        $id->schemeId = 'gln';
        $id->schemeAgencyId = '9';

        $xml = simplexml_load_string($this->getSerializer()->serialize($id, 'xml', $context));

        $this->assertEquals('ID', $xml->getName());
        $this->assertEquals($id, strval($xml));
        $this->assertEquals($id->schemeId, $xml['schemeID']);
        $this->assertEmpty($xml['schemeAgencyID']);
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->getDocNamespaces()
        );
    }

    public function testVersion10200(): void
    {
        $context = SerializationContext::create()->setVersion('1.2');

        $id = new Id();
        $id->id = '1442334659753';
        $id->schemeId = 'gln';
        $id->schemeAgencyId = '9';

        $xml = simplexml_load_string($this->getSerializer()->serialize($id, 'xml', $context));

        $this->assertEquals('ID', $xml->getName());
        $this->assertEquals($id, strval($xml));
        $this->assertEquals($id->schemeId, $xml['schemeID']);
        $this->assertEquals($id->schemeAgencyId, $xml['schemeAgencyID']);
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->getDocNamespaces()
        );
    }

    public function testVersion10000(): void
    {
        $context = SerializationContext::create()->setVersion('1.0');

        $id = new Id();
        $id->id = '1442334659753';
        $id->schemeId = 'gln';
        $id->schemeAgencyId = '9';

        $xml = simplexml_load_string($this->getSerializer()->serialize($id, 'xml', $context));

        $this->assertEquals('ID', $xml->getName());
        $this->assertEquals($id, strval($xml));
        $this->assertEmpty($xml['schemeID']);
        $this->assertEmpty($xml['schemeAgencyID']);
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->getDocNamespaces()
        );
    }

    protected function getSerializer(): Serializer
    {
        $builder = SerializerBuilder::create();

        return $builder->build();
    }
}
