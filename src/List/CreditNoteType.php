<?php

namespace DMT\Ubl\Service\List;

/**
 * https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL1001-cn/
 */
enum CreditNoteType: string
{
    /**
     * Document message used to provide credit information related to a transaction for goods or services to the
     * relevant party.
     */
    case CreditNoteRelatedToGoodsOrServices = '81';

    /**
     * Document message for providing credit information related to financial adjustments to the relevant party, e.g.,
     * bonuses.
     */
    case CreditNoteRelatedToFinancialAdjustments = '83';

    /**
     * (1113) Document/message for providing credit information to the relevant party.
     */
    case CreditNote = '381';

    /**
     * Credit note related to assigned invoice(s).
     */
    case FactoredCreditNote = '396';

    /**
     * Document/message for providing credit information to the relevant party.
     */
    case ForwardersCreditNote = '532';
}
