<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\Type\IdentificationCode;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class IdentificationCodeTest extends TestCase
{
    public function testSerializeVersionNlcius(): void
    {
        $context = SerializationContext::create()->setVersion(Invoice::VERSION_NLCIUS);

        $identificationCode = new IdentificationCode();
        $identificationCode->code = 'NL';

        $xml = simplexml_load_string($this->getSerializer()->serialize($identificationCode, 'xml', $context));

        $this->assertEquals('IdentificationCode', $xml->getName());
        $this->assertEquals($identificationCode, strval($xml));
        $this->assertEmpty($xml['listID']);
        $this->assertEmpty($xml['listAgencyID']);
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->getDocNamespaces()
        );
    }

    public function testSerializeVersion10200(): void
    {
        $context = SerializationContext::create()->setVersion(Invoice::VERSION_1_2);

        $identificationCode = new IdentificationCode();
        $identificationCode->code = 'NL';

        $xml = simplexml_load_string($this->getSerializer()->serialize($identificationCode, 'xml', $context));

        $this->assertEquals('IdentificationCode', $xml->getName());
        $this->assertEquals($identificationCode, strval($xml));
        $this->assertEquals($identificationCode->listId, $xml['listID']);
        $this->assertEquals($identificationCode->listAgencyId, $xml['listAgencyID']);
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
