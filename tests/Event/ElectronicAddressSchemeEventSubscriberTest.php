<?php

namespace DMT\Test\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\Type\ElectronicAddressType;
use DMT\Ubl\Service\Entity\Invoice\Type\EndpointId;
use DMT\Ubl\Service\Entity\Invoice\Type\Id;
use DMT\Ubl\Service\Event\ElectronicAddressSchemeEventSubscriber;
use DMT\Ubl\Service\List\ElectronicAddressScheme;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\SerializationContext;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ElectronicAddressSchemeEventSubscriberTest extends TestCase
{
    #[DataProvider(methodName: "provideGLNAddressType")]
    #[DataProvider(methodName: "provideBEVATAddressType")]
    #[DataProvider(methodName: "provideNLCommerceNumberAddressType")]
    public function testAddSchemeAttributesForSerialization(
        ElectronicAddressType $object,
        string $version,
        string $schemeId,
        ?string $schemeAgencyId
    ): void {
        $event = $this->getPreSerializeEvent($object, $version);

        $subscriber = new ElectronicAddressSchemeEventSubscriber();
        $subscriber->addSchemeAttributes($event);

        $this->assertSame($schemeId, $object->schemeId);
        $this->assertSame($schemeAgencyId, $object->schemeAgencyId);
    }

    #[DataProvider(methodName: "provideGLNAddressType")]
    #[DataProvider(methodName: "provideBEVATAddressType")]
    #[DataProvider(methodName: "provideNLCommerceNumberAddressType")]
    public function testAddSchemeAttributesAfterDeserialization(
        ElectronicAddressType $object,
        string $version,
        string $schemeId,
        ?string $schemeAgencyId
    ): void {
        $event = $this->getObjectEvent($object, $version);

        $subscriber = new ElectronicAddressSchemeEventSubscriber();
        $subscriber->addSchemeAttributes($event);

        $this->assertSame($schemeId, $object->schemeId);
        $this->assertSame($schemeAgencyId, $object->schemeAgencyId);
    }

    public static function provideGLNAddressType(): iterable
    {
        $object = new EndpointId();
        $object->id = '9982555125329';
        $object->schemeId = ElectronicAddressScheme::GLNNumber;

        yield 'GLN number (1.0)' => [$object, Invoice::VERSION_1_0, 'GLN', '9'];
        yield 'GLN number (1.1)' => [$object, Invoice::VERSION_1_1, 'GLN', '9'];
        yield 'GLN number (1.2)' => [$object, Invoice::VERSION_1_2, 'GLN', '9'];
        yield 'GLN number (2.0)' => [$object, Invoice::VERSION_2_0, '0088', null];
        yield 'GLN number (nlcius)' => [$object, Invoice::VERSION_NLCIUS, '0088', null];
    }

    public static function provideBEVATAddressType(): iterable
    {
        $object = new Id();
        $object->id = '1234433443';
        $object->schemeId = ElectronicAddressScheme::BEVatNumber;

        yield 'BE VAT number (1.0)' => [$object, Invoice::VERSION_1_0, 'BE:VAT', 'ZZZ'];
        yield 'BE VAT number (1.1)' => [$object, Invoice::VERSION_1_1, 'BE:VAT', 'ZZZ'];
        yield 'BE VAT number (1.2)' => [$object, Invoice::VERSION_1_2, 'BE:VAT', 'ZZZ'];
        yield 'BE VAT number (2.0)' => [$object, Invoice::VERSION_2_0, '9925', null];
    }

    public static function provideNLCommerceNumberAddressType(): iterable
    {
        $object = new Id();
        $object->id = '12344334';
        $object->schemeId = ElectronicAddressScheme::NLCommerceNumber;

        yield 'NL Chamber number (1.0)' => [$object, Invoice::VERSION_1_0, 'NL:KVK', 'ZZZ'];
        yield 'NL Chamber number (1.1)' => [$object, Invoice::VERSION_1_1, 'NL:KVK', 'ZZZ'];
        yield 'NL Chamber number (1.2)' => [$object, Invoice::VERSION_1_2, 'NL:KVK', 'ZZZ'];
        yield 'NL Chamber number (2.0)' => [$object, Invoice::VERSION_2_0, '0106', null];
    }

    protected function getPreSerializeEvent(object $object, $version): PreSerializeEvent
    {
        $name = $object::class;

        return new PreSerializeEvent(
            SerializationContext::create()->setAttribute('version', $version),
            $object,
            compact('name')
        );
    }

    protected function getObjectEvent(object $object, $version): ObjectEvent
    {
        $name = $object::class;

        return new ObjectEvent(
            DeserializationContext::create()->setAttribute('version', $version),
            $object,
            compact('name')
        );
    }
}
