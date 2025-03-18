<?php

namespace DMT\Ubl\Service\List;

enum InvoiceType: string
{
    case Normal = '380';
    case Debit = '383';
}
