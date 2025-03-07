<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice\Type\CompanyId;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class CompanyIdTest extends TestCase
{
    public function testSerializeVersion20000(): void
    {
        $context = SerializationContext::create()->setVersion('2.0');

        $companyId = new CompanyId();
        $companyId->id = '1442334659753';
        $companyId->schemeId = '0088';
        $companyId->schemeAgencyId = '6';

        $xml = simplexml_load_string($this->getSerializer()->serialize($companyId, 'xml', $context));

        $this->assertEquals('CompanyID', $xml->getName());
        $this->assertEquals($companyId, strval($xml));
        $this->assertEquals($companyId->schemeId, $xml['schemeID']);
        $this->assertEmpty($xml['schemeAgencyID']);
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->getDocNamespaces()
        );
    }

    public function testSerializeVersion10200(): void
    {
        $context = SerializationContext::create()->setVersion('1.2');

        $companyId = new CompanyId();
        $companyId->id = '01000332';
        $companyId->schemeId = 'NL:KVK';
        $companyId->schemeAgencyId = 'ZZZ';

        $xml = simplexml_load_string($this->getSerializer()->serialize($companyId, 'xml', $context));

        $this->assertEquals('CompanyID', $xml->getName());
        $this->assertEquals($companyId, strval($xml));
        $this->assertEquals($companyId->schemeId, $xml['schemeID']);
        $this->assertEquals($companyId->schemeAgencyId, $xml['schemeAgencyID']);
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
