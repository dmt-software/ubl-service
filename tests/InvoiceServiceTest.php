<?php

namespace DMT\Test\Ubl\Service;

use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\Entity\Invoice\Item;
use DMT\Ubl\Service\Entity\Invoice\LegalMonetaryTotal;
use DMT\Ubl\Service\Entity\Invoice\Price;
use DMT\Ubl\Service\Entity\Invoice\SellersItemIdentification;
use DMT\Ubl\Service\Entity\Invoice\Type\InvoicedQuantity;
use DMT\Ubl\Service\Entity\Invoice\Type\PayableAmount;
use DMT\Ubl\Service\Entity\Invoice\Type\PriceAmount;
use DMT\Ubl\Service\Entity\InvoiceLine;
use DMT\Ubl\Service\Helper\Invoice\AmountHelper;
use DMT\Ubl\Service\Helper\Invoice\QuantityHelper;
use DMT\Ubl\Service\InvoiceService;
use DMT\Ubl\Service\List\ElectronicAddressScheme;
use DMT\Ubl\Service\Objects\Invoice as InvoiceDTO;
use DMT\Ubl\Service\Objects\InvoiceLine as InvoiceLineDTO;
use DMT\Ubl\Service\Transformer\Invoice\InvoiceToSimpleObjectTransformer;
use DMT\Ubl\Service\Transformer\Invoice\SimpleObjectToInvoiceTransformer;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class InvoiceServiceTest extends TestCase
{
    #[DataProvider(methodName: 'providerCheckIdentifier')]
    public function testCheckIdentifier(string $identifier, string|ElectronicAddressScheme $scheme, string|false $expected): void
    {
        try {
            $this->assertSame($expected, (new InvoiceService())->checkIdentifier($identifier, $scheme));
        } catch (InvalidArgumentException) {
            $this->assertSame(false, $expected);
        }
    }

    public static function providerCheckIdentifier(): array
    {
        return [
            'valid ean code' => ['0000000000123', '0160', '0000000000123'],
            'invalid ean code' => ['0000000000124', '0160', false],
            'valid ean code using old code' => ['0000000000123', 'GTIN', '0000000000123'],
            'valid ean code with scheme' => ['0000000000123', ElectronicAddressScheme::GTINNumber, '0000000000123'],
            'valid NL commerce number' => ['01000332', ElectronicAddressScheme::NLCommerceNumber, '01000332'],
            'format LU vat number' => ['99287782', 'LU:VAT', 'LU99287782'],
        ];
    }

    public function testToXml(): void
    {
        $invoice = new Invoice();
        $invoice->invoiceLine[]= new InvoiceLine();

        $service = new InvoiceService();
        $this->assertStringContainsString('<Invoice', $service->toXml($invoice));
    }

    public function testFromXml(): void
    {
        $service = new InvoiceService();
        $invoice = $service->fromXml('<Invoice/>');

        $this->assertInstanceOf(Invoice::class, $invoice);
    }

    public function testFromInvoice(): void
    {
        $service = new InvoiceService();

        $invoice = new Invoice();
        $invoice->id = '376399';
        $invoice->legalMonetaryTotal = new LegalMonetaryTotal();
        $invoice->legalMonetaryTotal->payableAmount = AmountHelper::fetchFromValue(1.0, PayableAmount::class);

        $invoiceLine = new InvoiceLine();
        $invoiceLine->invoicedQuantity = QuantityHelper::fetchFromValue(1, InvoicedQuantity::class);
        $invoiceLine->price = new Price();
        $invoiceLine->price->priceAmount = AmountHelper::fetchFromValue(1.0, PriceAmount::class);
        $invoiceLine->item = new Item();
        $invoiceLine->item->sellersItemIdentification = new SellersItemIdentification();
        $invoiceLine->item->sellersItemIdentification->id = 'PRD-00282';

        $invoice->invoiceLine[] = $invoiceLine;

        $invoiceDTO = $service->fromInvoice($invoice, new InvoiceToSimpleObjectTransformer());

        $this->assertInstanceOf(InvoiceDTO::class, $invoiceDTO);
        $this->assertContainsOnlyInstancesOf(InvoiceLineDTO::class, $invoiceDTO->invoiceLines);
    }

    public function testToInvoice(): void
    {
        $service = new InvoiceService();

        $invoice = new InvoiceDTO('1234');
        $invoice->invoiceLines[] = new InvoiceLineDTO(
            product: 'article',
            amount: 3,
            price: 2.95,
            vatPercentage: 21,
            sku: 'PRD-002113'
        );

        $ublInvoice = $service->toInvoice($invoice, new SimpleObjectToInvoiceTransformer());

        $this->assertInstanceOf(Invoice::class, $ublInvoice);
        $this->assertContainsOnlyInstancesOf(InvoiceLine::class, $ublInvoice->invoiceLine);

    }
}
