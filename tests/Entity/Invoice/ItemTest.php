<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\BuyersItemIdentification;
use DMT\Ubl\Service\Entity\Invoice\Item;
use DMT\Ubl\Service\Entity\Invoice\SellersItemIdentification;
use DMT\Ubl\Service\Entity\Invoice\StandardItemIdentification;
use DMT\Ubl\Service\Entity\Invoice\Type\Id;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    public function testSerialize(): void
    {
        $item = new Item();
        $item->name = 'Product 123';
        $item->buyersItemIdentification = new BuyersItemIdentification();
        $item->buyersItemIdentification->id = '123';
        $item->sellersItemIdentification = new SellersItemIdentification();
        $item->sellersItemIdentification->id = '123';
        $item->standardItemIdentification = new StandardItemIdentification();
        $item->standardItemIdentification->id = new Id();
        $item->standardItemIdentification->id->id = '1234567899992';

        $xml = simplexml_load_string($this->getSerializer()->serialize($item, 'xml'));

        $this->assertEquals('Item', $xml->getName());
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="BuyersItemIdentification"]')[0]->getNamespaces()
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="SellersItemIdentification"]')[0]->getNamespaces()
        );
        $this->assertcontains(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
            $xml->xpath('*[local-name()="StandardItemIdentification"]')[0]->getNamespaces()
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
