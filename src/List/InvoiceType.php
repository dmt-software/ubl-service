<?php

namespace DMT\Ubl\Service\List;

/**
 * https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL1001-inv/
 */
enum InvoiceType: string
{
    case RequestForPayment = '71';
    case DebitNoteRelatedToGoodsOrServices = '80';
    case MeteredServicesInvoice = '82';
    case DebitNoteRelatedToFinancialAdjustment = '84';
    case TaxNotification = '102';
    case FinalPaymentRequestBasedOnCompletionOfWork = '218';
    case PaymentRequestForCompletedUnits = '219';
    case PartialInvoice = '326';
    case CommercialInvoiceWhichIncludesAPackingList = '331';
    case CommercialInvoice = '380';
    case CommisionNote = '382';
    case DebitNote = '383';
    case CorrectedInvoice = '384';
    case PrepaymentInvoice = '386';
    case TaxInvoice = '388';
    case SelfBilledInvoice = '389';
    case FactoredInvoice = '393';
    case ConsignmentInvoice = '395';
    case ForwardersInvoiceDiscrepancyReport = '553';
    case InsurersInvoice = '575';
    case ForwardersInvoice = '623';
    case FreightInvoice = '780';
    case ClaimNotification = '817';
    case ConsularInvoice = '870';
    case PartialConstructionInvoice = '875';
    case PartialFinalConstructionInvoice = '876';
    case FinalConstructionInvoice = '877';
}
