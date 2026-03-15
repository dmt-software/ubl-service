<?php

namespace DMT\Ubl\Service\List;

/**
 * https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL1001-inv/
 */
enum InvoiceType: string
{
    /**
     * Document/message issued by a creditor to a debtor to request payment of one or more invoices past due.
     */
    case RequestForPayment = '71';

    /**
     * Document/message issued by a creditor to a debtor to request payment of one or more invoices past due.
     */
    case DebitNoteRelatedToGoodsOrServices = '80';

    /**
     * Document/message claiming payment for the supply of metered services (e.g., gas, electricity, etc.) supplied to
     * a fixed meter whose consumption is measured over a period of time.
     */
    case MeteredServicesInvoice = '82';

    /**
     * Document/message for providing debit information related to financial adjustments to the relevant party.
     */
    case DebitNoteRelatedToFinancialAdjustment = '84';

    /**
     * Used to specify that the message is a tax notification.
     */
    case TaxNotification = '102';

    /**
     * The final payment request of a series of payment requests submitted upon completion of all the work.
     */
    case FinalPaymentRequestBasedOnCompletionOfWork = '218';

    /**
     * A request for payment for completed units.
     */
    case PaymentRequestForCompletedUnits = '219';

    /**
     * Document/message specifying details of an incomplete invoice.
     */
    case PartialInvoice = '326';

    /**
     * Commercial transaction (invoice) will include a packing list.
     */
    case CommercialInvoiceWhichIncludesAPackingList = '331';

    /**
     * (1334) Document/message claiming payment for goods or services supplied under conditions agreed between seller
     * and buyer.
     */
    case CommercialInvoice = '380';

    /**
     * Document/message in which a seller specifies the amount of commission, the percentage of the invoice amount, or
     * some other basis for the calculation of the commission to which a sales agent is entitled.
     */
    case CommissionNote = '382';

    /**
     * Document/message for providing debit information to the relevant party.
     */
    case DebitNote = '383';

    /**
     * Commercial invoice that includes revised information differing from an earlier submission of the same invoice.
     */
    case CorrectedInvoice = '384';

    /**
     * An invoice to pay amounts for goods and services in advance; these amounts will be subtracted from the final
     * invoice.
     */
    case PrepaymentInvoice = '386';

    /**
     * An invoice for tax purposes.
     */
    case TaxInvoice = '388';

    /**
     * An invoice the invoicee is producing instead of the seller.
     */
    case SelfBilledInvoice = '389';

    /**
     * Invoice assigned to a third party for collection.
     */
    case FactoredInvoice = '393';

    /**
     * Commercial invoice that covers a transaction other than one involving a sale.
     */
    case ConsignmentInvoice = '395';

    /**
     * Document/message reporting invoice discrepancies indentified by the forwarder.
     */
    case ForwardersInvoiceDiscrepancyReport = '553';

    /**
     * Document/message issued by an insurer specifying the cost of an insurance which has been effected and claiming
     * payment therefore.
     */
    case InsurersInvoice = '575';

    /**
     * Invoice issued by a freight forwarder specifying services rendered and costs incurred and claiming payment
     * therefore.
     */
    case ForwardersInvoice = '623';

    /**
     * Document/message issued by a transport operation specifying freight costs and charges incurred for a transport
     * operation and stating conditions of payment.
     */
    case FreightInvoice = '780';

    /**
     * Document notifying a claim.
     */
    case ClaimNotification = '817';

    /**
     * Document/message to be prepared by an exporter in his country and presented to a diplomatic representation of
     * the importing country for endorsement and subsequently to be presented by the importer in connection with the
     * import of the goods described therein.
     */
    case ConsularInvoice = '870';

    /**
     * Partial invoice in the context of a specific construction project.
     */
    case PartialConstructionInvoice = '875';

    /**
     * Invoice concluding all previous partial construction invoices of a completed partial rendered service in the
     * context of a specific construction project.
     */
    case PartialFinalConstructionInvoice = '876';

    /**
     * Invoice concluding all previous partial invoices and partial final construction invoices in the context of a
     * specific construction project.
     */
    case FinalConstructionInvoice = '877';
}
