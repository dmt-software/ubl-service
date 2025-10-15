<?php

namespace DMT\Test\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Event\InvoiceCustomizationEventSubscriber;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\SerializationContext;
use PHPUnit\Framework\TestCase;

class InvoiceCustomizationEventSubscriberTest extends TestCase
{
    public function testEventSetsCustomization(): void
    {
        $subscribing = new InvoiceCustomizationEventSubscriber();

        $invoice = new Invoice();

        $this->assertNull($invoice->ublVersionId);
        $this->assertNull($invoice->customizationId);
        $this->assertNull($invoice->profileId);

        $subscribing->setCustomization(
            $this->getPreSerializeEvent($invoice)
        );

        $this->assertNotNull($invoice->ublVersionId);
        $this->assertNotNull($invoice->customizationId);
        $this->assertNotNull($invoice->profileId);

    }

    protected function getPreSerializeEvent(object $object): PreSerializeEvent
    {
        $name = $object::class;

        return new PreSerializeEvent(
            SerializationContext::create()->setAttribute('version', null),
            $object,
            compact('name')
        );
    }
}
