<?php

namespace DMT\Ubl\Service\List;

/**
 * https://docs.peppol.eu/poacc/billing/3.0/syntax/ubl-creditnote/cac-AdditionalDocumentReference/cbc-DocumentTypeCode/
 */
enum DocumentType: string
{
    case InvoiceObjectReference = '130';
    case ProjectReference = '50';
}
