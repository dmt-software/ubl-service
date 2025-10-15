<?php

namespace DMT\Test\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\OrderReference;
use DMT\Ubl\Service\Entity\Invoice\Party;
use DMT\Ubl\Service\Entity\Invoice\PartyLegalEntity;
use DMT\Ubl\Service\Entity\Invoice\PartyName;
use DMT\Ubl\Service\Entity\InvoiceLine;
use DMT\Ubl\Service\Event\MandatoryDefaultsEventSubscriber;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\SerializationContext;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MandatoryDefaultsEventSubscriberTest extends TestCase
{
    #[DataProvider(methodName: 'provideInvoiceForOrderReference')]
    public function testSetDefaultOrderReference(Invoice $invoice, string $version, null|string $expected): void
    {
        $event = new PreSerializeEvent(
            SerializationContext::create()->setAttribute('version', $version),
            $invoice,
            ['name' => Invoice::class]
        );

        $subscriber = new MandatoryDefaultsEventSubscriber();
        $subscriber->setDefaultOrderReference($event);

        $this->assertSame($expected, $invoice->orderReference?->id);
    }

    public static function provideInvoiceForOrderReference(): iterable
    {
        yield 'skip for version 1.0' => [new Invoice(), Invoice::VERSION_1_0, null];
        yield 'skip for version 1,1' => [new Invoice(), Invoice::VERSION_1_1, null];
        yield 'skip for version 1.2' => [new Invoice(), Invoice::VERSION_1_2, null];
        yield 'set default for version 2.0' => [new Invoice(), Invoice::VERSION_2_0, 'NA'];
        yield 'set default for version 2.0.0-nlcius' => [new Invoice(), Invoice::VERSION_NLCIUS, 'NA'];

        $invoice = new Invoice();
        $invoice->orderReference = new OrderReference();
        $invoice->orderReference->id = '2287663';

        yield 'not override set reference' => [$invoice, Invoice::VERSION_2_0, '2287663'];
    }

    #[DataProvider(methodName: 'provideInvoiceForLineNumberDefaults')]
    public function testSetDefaultInvoiceLineNumbers(Invoice $invoice, int $expected): void
    {
        $event = new PreSerializeEvent(
            SerializationContext::create(),
            $invoice,
            ['name' => Invoice::class]
        );

        $subscriber = new MandatoryDefaultsEventSubscriber();
        $subscriber->setDefaultInvoiceLineNumbers($event);

        $this->assertEquals($expected, $invoice->invoiceLine[0]->id);
    }

    public static function provideInvoiceForLineNumberDefaults(): iterable
    {
        $invoice = new Invoice();
        $invoice->invoiceLine = [
            new InvoiceLine(),
            new InvoiceLine(),
            new InvoiceLine(),
        ];

        yield 'set unset line numbers' => [$invoice, 1];

        $invoice1 = new Invoice();
        $invoice1->invoiceLine = [
            new InvoiceLine(),
            new InvoiceLine(),
            new InvoiceLine(),
        ];
        $invoice1->invoiceLine[1]->id = '16';

        yield 'partial set line numbers' => [$invoice1, 17];

        $invoice2 = new Invoice();
        $invoice2->invoiceLine = [
            new InvoiceLine(),
            new InvoiceLine(),
            new InvoiceLine(),
        ];
        $invoice2->invoiceLine[0]->id = '18773';
        $invoice2->invoiceLine[1]->id = '18663';
        $invoice2->invoiceLine[2]->id = '18553';

        yield 'not override set line numbers' => [$invoice2, 18773];
    }

    #[DataProvider(methodName: 'providePartyForDefaultLegalName')]
    public function testSetPartyDefaults(Party $party, string $version, null|string $expected): void
    {
        $event = new PreSerializeEvent(
            SerializationContext::create()->setAttribute('version', $version),
            $party,
            ['name' => Invoice::class]
        );

        $subscriber = new MandatoryDefaultsEventSubscriber();
        $subscriber->setPartyDefaultLegalName($event);

        $this->assertSame($expected, $party?->partyLegalEntity?->registrationName);
    }

    public static function providePartyForDefaultLegalName(): iterable
    {
        $party = new Party();
        $party->partyName = new PartyName();
        $party->partyName->name = 'Business';

        yield 'skip for version 1.0' => [clone($party), Invoice::VERSION_1_0, null];
        yield 'skip for version 1,1' => [clone($party), Invoice::VERSION_1_1, null];
        yield 'skip for version 1.2' => [clone($party), Invoice::VERSION_1_2, 'Business'];
        yield 'set default for version version 2.0' => [clone($party), Invoice::VERSION_2_0, 'Business'];
        yield 'set default for version 2.0.0-nlcius' => [clone($party), Invoice::VERSION_NLCIUS, 'Business'];

        $party1 = clone($party);
        $party1->partyLegalEntity = new PartyLegalEntity();
        $party1->partyLegalEntity->registrationName = 'Business BV';

        yield 'not override set default' => [$party1, Invoice::VERSION_2_0, 'Business BV'];
        yield 'no party name, no legal name' => [new Party(), Invoice::VERSION_2_0, null];
    }
}
