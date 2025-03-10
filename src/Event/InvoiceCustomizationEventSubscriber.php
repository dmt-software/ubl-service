<?php

namespace DMT\Ubl\Service\Event;

use DMT\Ubl\Service\Entity\Invoice;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

readonly class InvoiceCustomizationEventSubscriber implements EventSubscriberInterface
{
    public const string CUSTOMIZATION_DEFAULT =
        'urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0';
    public const string CUSTOMIZATION_1_0 = 'urn:www.cenbii.eu:transaction:biicoretrdm010:ver1.0:'
        . '#urn:www.peppol.eu:bis:peppol4a:ver1.0#urn:www.simplerinvoicing.org:si-ubl:invoice:ver1.0.x';
    public const string CUSTOMIZATION_1_1 = 'urn:www.cenbii.eu:transaction:biitrns010:ver2.0:'
        . 'extended:urn:www.peppol.eu:bis:peppol4a:ver2.0:extended:urn:www.simplerinvoicing.org:si:si-ubl:ver1.1.x';
    public const string CUSTOMIZATION_1_2 = 'urn:www.cenbii.eu:transaction:biitrns010:ver2.0:'
        . 'extended:urn:www.peppol.eu:bis:peppol4a:ver2.0:extended:urn:www.simplerinvoicing.org:si:si-ubl:ver1.2';
    public const string CUSTOMIZATION_2_0 = 'urn:cen.eu:en16931:2017#compliant#urn:fdc:nen.nl:nlcius:v1.0';

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): iterable
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'interface' => Invoice::class,
                'method' => 'setCustomization',
                'format' => 'xml'
            ]
        ];
    }

    public function setCustomization(PreSerializeEvent $event): void
    {
        /** @var Invoice $invoice */
        $invoice = $event->getObject();
        $version = $event->getContext()->getAttribute('version');

        $invoice->customizationId = match ($version) {
            '1.0' => self::CUSTOMIZATION_1_0,
            '1.1' => self::CUSTOMIZATION_1_1,
            '1.2' => self::CUSTOMIZATION_1_2,
            '2.0' => self::CUSTOMIZATION_2_0,
            default => self::CUSTOMIZATION_DEFAULT,
        };

        $invoice->ublVersionId = '2.1';
        if ($version == '1.0') {
            $invoice->ublVersionId = '2.0';
        }

        $invoice->profileId = 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0';
        if (version_compare($version, '1.2', '<=')) {
            $invoice->profileId = 'urn:www.cenbii.eu:profile:bii04:ver1.0';
        }
    }
}