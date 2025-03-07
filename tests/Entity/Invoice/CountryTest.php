<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Country;
use DMT\Ubl\Service\Entity\Invoice\Type\IdentificationCode;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class CountryTest extends TestCase
{
    public function testSerialize(): void
    {
        $country = new Country();
        $country->identificationCode = new IdentificationCode();
        $country->identificationCode->code = 'NL';

        $xml = simplexml_load_string($this->getSerializer()->serialize($country, 'xml'));

        $this->assertEquals('Country', $xml->getName());
        $this->assertEquals(
            strval($country->identificationCode),
            strval($xml->xpath('*[local-name()="IdentificationCode"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="IdentificationCode"]')[0]->getNamespaces()
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
