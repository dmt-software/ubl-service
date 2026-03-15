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
use DMT\Ubl\Service\UblService;

/** @var Invoice $invoice */
$service = new UblService();
print $service->toXml($invoice);

// <Invoice xmlns:cac="...">...</Invoice>
```

#### Process an UBL Invoice XML

```php
use DMT\Ubl\Service\UblService;

$service = new UblService();
$invoice = $service->fromXml('<Invoice xmlns:cac="...">...</Invoice>');

// process the invoice
```

### Transformers

#### Create UBL Invoice from DTO

```php
use DMT\Ubl\Service\UblService;
use DMT\Ubl\Service\Transformer\Invoice\SimpleObjectToInvoiceTransformer;
use DMT\Ubl\Service\Objects\Invoice;

/** @var Invoice $invoice */
$service = new UblService();
$ublInvoice = $service->toInvoice($invoice, new SimpleObjectToInvoiceTransformer());
```

#### Fetch DTO from UBL Invoice

```php
use DMT\Ubl\Service\UblService;
use DMT\Ubl\Service\Transformer\Invoice\InvoiceLineToSimpleObjectTransformer;
use DMT\Ubl\Service\Entity\Invoice;

/** @var Invoice $ublInvoice */
$service = new UblService();
$invoice = $service->fromInvoice($ublInvoice, new InvoiceLineToSimpleObjectTransformer());
```

### Validation

```php
use DMT\Ubl\Service\UblService;
use InvalidArgumentException;

$service = new UblService();
try {
    // test (and formats) the identifier
    $identifier = $service->checkIdentifier('0000000000123', 'GTIN');
} catch (InvalidArgumentException) {
    // identifier is invalid
}
```

### Different UBL versions

By default `toXml` will return an UBL Invoice in PEPPOL BIS Billing 3.0 format.  
To change the format you can add the correct format in the call:

```php
use DMT\Ubl\Service\Entity\Invoice;
use DMT\Ubl\Service\UblService;

/** @var Invoice $invoice */
$service = new UblService();
print $service->toXml($invoice, Invoice::VERSION_NLCIUS);

// output is according to the NLCIUS format
```

It is possible to change format from an existing XML by creating an invoice using `fromXML` followed by `toXML` with a 
different format.

> NOTE: When changing format, especially from new to older one, some data may be lost or invalid.
