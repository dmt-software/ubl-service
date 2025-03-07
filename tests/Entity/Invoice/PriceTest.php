<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Price;
use DMT\Ubl\Service\Entity\Invoice\Type\BaseQuantity;
use DMT\Ubl\Service\Entity\Invoice\Type\PriceAmount;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class PriceTest extends TestCase
{
    public function testSerialize(): void
    {
        $price = new Price();
        $price->priceAmount = new PriceAmount();
        $price->priceAmount->amount = 44.97800001;
        $price->priceAmount->currencyId = 'EUR';
        $price->baseQuantity = new BaseQuantity();
        $price->baseQuantity->quantity = 3;

        $xml = simplexml_load_string($this->getSerializer()->serialize($price, 'xml'));

        $this->assertEquals('Price', $xml->getName());
        $this->assertStringStartsWith(
            strval($xml->xpath('*[local-name()="PriceAmount"]')[0]),
            strval($price->priceAmount),
        );
        $this->assertStringEndsWith(
            strval($xml->xpath('*[local-name()="PriceAmount"]/@currencyId')[0]),
            strval($price->priceAmount)
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="PriceAmount"]')[0]->getNamespaces()
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->xpath('*[local-name()="BaseQuantity"]')[0]->getNamespaces()
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
