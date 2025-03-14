<?php

namespace DMT\Test\Ubl\Service\Entity\Invoice\Type;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\Type\CompanyId;
use DMT\Ubl\Service\Event\ElectronicAddressSchemeEventSubscriber;
use DMT\Ubl\Service\List\ElectronicAddressScheme;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class CompanyIdTest extends TestCase
{
    public function testSerializeVersionNlcius(): void
    {
        $context = SerializationContext::create()->setVersion(Invoice::VERSION_NLCIUS);

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
        $context = SerializationContext::create()->setVersion(Invoice::VERSION_1_2);

        $companyId = new CompanyId();
        $companyId->id = 'NL010003321B01';
        $companyId->schemeId = ElectronicAddressScheme::NLCommerceNumber;

        $xml = simplexml_load_string($this->getSerializer()->serialize($companyId, 'xml', $context));

        $this->assertEquals('CompanyID', $xml->getName());
        $this->assertEquals($companyId, strval($xml));
        $this->assertEquals(ElectronicAddressScheme::NLCommerceNumber->getSchemeId(Invoice::VERSION_1_2), $xml['schemeID']);
        $this->assertEquals(ElectronicAddressScheme::NLCommerceNumber->getSchemeAgencyId(Invoice::VERSION_1_2), $xml['schemeAgencyID']);
        $this->assertContainsEquals(
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2',
            $xml->getDocNamespaces()
        );
    }

    protected function getSerializer(): Serializer
    {
        $builder = SerializerBuilder::create();
        $builder->enableEnumSupport();
        $builder->configureListeners(function (EventDispatcher $dispatcher) {
            $dispatcher->addSubscriber(new ElectronicAddressSchemeEventSubscriber());
        });

        return $builder->build();
    }
}
