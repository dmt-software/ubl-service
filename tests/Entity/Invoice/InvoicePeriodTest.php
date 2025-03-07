<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\InvoicePeriod;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class InvoicePeriodTest extends TestCase
{
    public function testSerialize(): void
    {
        $invoicingPeriod = new InvoicePeriod();
        $invoicingPeriod->startDate = new \DateTime('2025-03-12');
        $invoicingPeriod->endDate = new \DateTime('2025-03-15');

        $xml = simplexml_load_string($this->getSerializer()->serialize($invoicingPeriod, 'xml'));

        $this->assertEquals('InvoicePeriod', $xml->getName());
        $this->assertEquals(
            $invoicingPeriod->startDate->format('Y-m-d'),
            strval($xml->xpath('*[local-name()="StartDate"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="StartDate"]')[0]->getNamespaces()
        );
        $this->assertEquals(
            $invoicingPeriod->endDate->format('Y-m-d'),
            strval($xml->xpath('*[local-name()="EndDate"]')[0])
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="EndDate"]')[0]->getNamespaces()
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
