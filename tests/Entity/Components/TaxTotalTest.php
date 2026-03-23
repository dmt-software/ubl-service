<?php

namespace DMT\Test\Ubl\Service\Entity\Components;

use DMT\Ubl\Service\Entity\CommonAggregateComponents\TaxTotal;
use DMT\Ubl\Service\Entity\CommonBasicComponents\Amount;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class TaxTotalTest extends TestCase
{
    public function testSerialize(): void
    {
        $taxTotal = new TaxTotal();
        $taxTotal->taxAmount = new Amount();
        $taxTotal->taxAmount->amount = 123.00;
        $taxTotal->taxAmount->currencyId = 'USD';

        $xml = simplexml_load_string($this->getSerializer()->serialize($taxTotal, 'xml'));

        $this->assertEquals('TaxTotal', $xml->getName());
        $this->assertStringStartsWith(
            strval($xml->xpath('*[local-name()="TaxAmount"]')[0]),
            strval($taxTotal->taxAmount)
        );
        $this->assertStringEndsWith(
            strval($xml->xpath('*[local-name()="TaxAmount"]/@currencyID')[0]),
            strval($taxTotal->taxAmount)
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="TaxAmount"]')[0]->getNamespaces()
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
