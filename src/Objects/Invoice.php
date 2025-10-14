<?php

namespace DMT\Ubl\Service\Objects;

use DateTime;

class Invoice
{
    public null|Party $buyer = null;
    public null|Party $seller = null;
    public null|Address $address = null;
    public array $invoiceLines = [];

    /**
     * @param int $documentId The document id of the invoice.
     * @param DateTime $invoiceDate The invoice date.
     * @param DateTime|null $dueDate The date before the invoice should be paid.
     * @param string|null $invoiceType The type of invoice.
     * @param string|null $orderReference The order reference given by the buyer.
     * @param string|null $salesOrderReference The order reference given by the supplier.
     * @param string|null $paymentTerm The term(s) for the payment.
     * @param array<DateTime>|null $invoicePeriod The invoice or delivery period.
     * @param float $total The total amount (to be) paid.
     */
    public function __construct(
        public int $documentId,
        public DateTime $invoiceDate = new DateTime(),
        public null|DateTime $dueDate = null,
        public null|string $invoiceType = null,
        public null|string $orderReference = null,
        public null|string $salesOrderReference = null,
        public null|string $paymentTerm = null,
        public null|array $invoicePeriod = null,
        public float $total = 0.0,
    ) {
    }
}
