<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\Type\EndpointId;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class EndpointIdTest extends TestCase
{
    public function testSerializeVersionNlcius(): void
    {
        $context = SerializationContext::create()->setVersion(Invoice::VERSION_NLCIUS);

        $endpointId = new EndpointId();
        $endpointId->id = '1442334659753';
        $endpointId->schemeId = 'gln';
        $endpointId->schemeAgencyId = '9';

        $xml = simplexml_load_string($this->getSerializer()->serialize($endpointId, 'xml', $context));

        $this->assertEquals('EndpointID', $xml->getName());
        $this->assertEquals($endpointId, strval($xml));
        $this->assertEquals($endpointId->schemeId, $xml['schemeID']);
        $this->assertEmpty($xml['schemeAgencyID']);
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->getDocNamespaces()
        );
    }

    public function testSerializeVersion10200(): void
    {
        $context = SerializationContext::create()->setVersion(Invoice::VERSION_1_2);

        $endpointId = new EndpointId();
        $endpointId->id = '1442334659753';
        $endpointId->schemeId = 'gln';
        $endpointId->schemeAgencyId = '9';

        $xml = simplexml_load_string($this->getSerializer()->serialize($endpointId, 'xml', $context));

        $this->assertEquals('EndpointID', $xml->getName());
        $this->assertEquals($endpointId, strval($xml));
        $this->assertEquals($endpointId->schemeId, $xml['schemeID']);
        $this->assertEquals($endpointId->schemeAgencyId, $xml['schemeAgencyID']);
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
