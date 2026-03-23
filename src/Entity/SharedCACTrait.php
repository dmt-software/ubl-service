<?php

namespace DMT\Ubl\Service\Entity;

use DMT\Ubl\Service\Entity\CommonAggregateComponents\AccountingCustomerParty;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\AccountingSupplierParty;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\AdditionalDocumentReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\AllowanceCharge;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\BillingReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Delivery;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\DespatchDocumentReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\Period;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\LegalMonetaryTotal;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\OrderReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PaymentMeans;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\PaymentTerms;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\ReceiptDocumentReference;
use DMT\Ubl\Service\Entity\CommonAggregateComponents\TaxTotal;
use Generator;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlList;

trait SharedCACTrait
{
    #[SerializedName(name: "InvoicePeriod")]
    #[Since(version: Versions::VERSION_1_1)]
    #[Type(name: Period::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|Period $invoicePeriod = null;

    #[SerializedName(name: "OrderReference")]
    #[Since(version: Versions::VERSION_1_1)]
    #[Type(name: OrderReference::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|OrderReference $orderReference = null;

    #[Type(name: 'array<' . AdditionalDocumentReference::class . '>')]
    #[XmlList(
        entry: "AdditionalDocumentReference",
        inline: true,
        namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
    )]
    /** @var array<AdditionalDocumentReference> $additionalDocumentReference */
    public null|array $additionalDocumentReference = null;

    #[Type(name: 'array<' . BillingReference::class . '>')]
    #[XmlList(
        entry: "BillingReference",
        inline: true,
        namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
    )]
    /** @var array<BillingReference> $billingReference */
    public array $billingReference = [];

    #[Type(name: 'array<' . DespatchDocumentReference::class . '>')]
    #[XmlList(
        entry: "DespatchDocumentReference",
        inline: true,
        namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
    )]
    /** @var array<DespatchDocumentReference> $despatchDocumentReference */
    public array $despatchDocumentReference = [];

    #[Type(name: 'array<' . ReceiptDocumentReference::class . '>')]
    #[XmlList(
        entry: "ReceiptDocumentReference",
        inline: true,
        namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
    )]
    /** @var array<ReceiptDocumentReference> $receiptDocumentReference */
    public array $receiptDocumentReference = [];

    // OriginatorDocumentReference
    // ContractDocumentReference

    // ProjectReference

    #[SerializedName(name: "AccountingSupplierParty")]
    #[Type(name: AccountingSupplierParty::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|AccountingSupplierParty $accountingSupplierParty = null;

    #[SerializedName(name: "AccountingCustomerParty")]
    #[Type(name: AccountingCustomerParty::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|AccountingCustomerParty $accountingCustomerParty = null;

    // PayeeParty
    // TaxRepresentativeParty

    #[SerializedName(name: "Delivery")]
    #[Since(version: Versions::VERSION_1_1)]
    #[Type(name: Delivery::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|Delivery $delivery = null;

    #[Type(name: 'array<' . PaymentMeans::class . '>')]
    #[XmlList(
        entry: "PaymentMeans",
        inline: true,
        namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
    )]
    /** @var array<PaymentMeans> $paymentMeans */
    public null|array $paymentMeans = null;

    #[SerializedName(name: "PaymentTerms")]
    #[Type(name: PaymentTerms::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|PaymentTerms $paymentTerms = null;

    #[Type(name: 'array<' . AllowanceCharge::class . '>')]
    #[XmlList(
        entry: "AllowanceCharge",
        inline: true,
        namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
    )]
    /** @var array<AllowanceCharge> $allowanceCharge */
    public null|array $allowanceCharge = null;

    #[SerializedName(name: "TaxTotal")]
    #[Type(name: TaxTotal::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|TaxTotal $taxTotal = null;

    #[SerializedName(name: "LegalMonetaryTotal")]
    #[Type(name: LegalMonetaryTotal::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|LegalMonetaryTotal $legalMonetaryTotal = null;

    public function getEmbeddedFiles(string $mimeCode = 'application/pdf'): Generator
    {
        if (!isset($this->additionalDocumentReference)) {
            return;
        }

        foreach($this->additionalDocumentReference as $documentReference) {
            $embeddedDocumentBinaryObject = $documentReference->attachment->embeddedDocumentBinaryObject ?? null;

            if (!$embeddedDocumentBinaryObject) {
                continue;
            }

            if ($embeddedDocumentBinaryObject->mimeCode != $mimeCode) {
                continue;
            }

            yield $embeddedDocumentBinaryObject->filename => strval($embeddedDocumentBinaryObject);
        }
    }
}
