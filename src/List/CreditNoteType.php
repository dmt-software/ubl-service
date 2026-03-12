<?php

namespace DMT\Ubl\Service\List;

/**
 * https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL1001-cn/
 */
enum CreditNoteType: string
{
    case CreditNoteRelatedToGoodsOrServices = '81';
    case CreditNoteRelatedToFinancialAdjustments = '83';
    case CreditNote = '381';
    case FactoredCreditNote = '396';
    case ForwardersCreditNote = '532';
}
