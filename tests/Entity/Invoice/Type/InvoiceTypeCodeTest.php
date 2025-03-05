<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice\Type\InvoiceTypeCode;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class InvoiceTypeCodeTest extends TestCase
{
    public function testVersion20000(): void
    {
        $context = SerializationContext::create()->setVersion('2.0');

        $invoiceTypeCode = new InvoiceTypeCode();
        $invoiceTypeCode->code = 'NL';

        $xml = simplexml_load_string($this->getSerializer()->serialize($invoiceTypeCode, 'xml', $context));

        $this->assertEquals('InvoiceTypeCode', $xml->getName());
        $this->assertEquals($invoiceTypeCode, strval($xml));
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

        $invoiceTypeCode = new InvoiceTypeCode();
        $invoiceTypeCode->code = 'NL';

        $xml = simplexml_load_string($this->getSerializer()->serialize($invoiceTypeCode, 'xml', $context));

        $this->assertEquals('InvoiceTypeCode', $xml->getName());
        $this->assertEquals($invoiceTypeCode, strval($xml));
        $this->assertEquals($invoiceTypeCode->listId, $xml['listID']);
        $this->assertEquals($invoiceTypeCode->listAgencyId, $xml['listAgencyID']);
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
