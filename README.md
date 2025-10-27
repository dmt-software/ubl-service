# UBL service

Service to create and process UBL xml documents.

## Install
```bash
composer require dmt-software/ubl-service
```

## Usage

### Invoice XML 

#### Create an UBL Invoice XML

```php
use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\InvoiceService;

/** @var Invoice $invoice */
$service = new InvoiceService();
print $service->toXml($invoice);

// <Invoice xmlns:cac="...">...</Invoice>
```

#### Process an UBL Invoice XML

```php
use DMT\Ubl\Service\InvoiceService;

$service = new InvoiceService();
$invoice = $service->fromXml('<Invoice xmlns:cac="...">...</Invoice>');

// process the invoice
```

### Transformers

#### Create UBL Invoice from DTO

```php
use DMT\Ubl\Service\InvoiceService;
use DMT\Ubl\Service\Transformer\Invoice\SimpleObjectToInvoiceTransformer;
use DMT\Ubl\Service\Objects\Invoice;

/** @var Invoice $invoice */
$service = new InvoiceService();
$ublInvoice = $service->toInvoice($invoice, new SimpleObjectToInvoiceTransformer());
```

#### Fetch DTO from UBL Invoice

```php
use DMT\Ubl\Service\InvoiceService;
use DMT\Ubl\Service\Transformer\Invoice\InvoiceLineToSimpleObjectTransformer;
use DMT\Ubl\Service\Entity\Invoice;

/** @var Invoice $ublInvoice */
$service = new InvoiceService();
$invoice = $service->fromInvoice($ublInvoice, new InvoiceLineToSimpleObjectTransformer());
```

### Validation

```php
use DMT\Ubl\Service\InvoiceService;

$service = new InvoiceService();
if ($service->checkIdentifier('0000000000123', 'GTIN')) {
    // the number format is valid. (does not tell anything about the existence)
}
```

