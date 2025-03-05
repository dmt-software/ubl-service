<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice\Type\DocumentCurrencyCode;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class DocumentCurrencyCodeTest extends TestCase
{
    public function testVersion20000(): void
    {
        $context = SerializationContext::create()->setVersion('2.0');

        $documentCurrencyCode = new DocumentCurrencyCode();
        $documentCurrencyCode->code = 'EUR';

        $xml = simplexml_load_string($this->getSerializer()->serialize($documentCurrencyCode, 'xml', $context));

        $this->assertEquals('DocumentCurrencyCode', $xml->getName());
        $this->assertEquals($documentCurrencyCode, strval($xml));
        $this->assertEmpty($xml['listID']);
        $this->assertEmpty($xml['listAgencyID']);
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->getDocNamespaces()
        );
    }

    public function testVersion10200(): void
    {
        $context = SerializationContext::create()->setVersion('1.2');

        $documentCurrencyCode = new DocumentCurrencyCode();
        $documentCurrencyCode->code = 'EUR';

        $xml = simplexml_load_string($this->getSerializer()->serialize($documentCurrencyCode, 'xml', $context));

        $this->assertEquals('DocumentCurrencyCode', $xml->getName());
        $this->assertEquals($documentCurrencyCode, strval($xml));
        $this->assertEquals($documentCurrencyCode->listId, $xml['listID']);
        $this->assertEquals($documentCurrencyCode->listAgencyId, $xml['listAgencyID']);
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
