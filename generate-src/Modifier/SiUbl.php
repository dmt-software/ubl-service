<?php

namespace DMT\Ubl\Generate\Modifier;

use DMT\Ubl\Generate\Schema\XsdSchemaCollection;

class SiUbl
{
    // https://docs.peppol.eu/poacc/billing/3.0/rules/ubl-tc434/
    public function modify(XsdSchemaCollection $c): void
    {
        $cac = 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2';
        $cbc = 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2';
        $cre = 'urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2';
        $inv = 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2';

        // BR-01 fatal
        // An Invoice shall have a Specification identifier (BT-24).
        $c->namespaces[$inv]->elements['cbc:CustomizationID']->minOccurs = '1';
        $c->namespaces[$cre]->elements['cbc:CustomizationID']->minOccurs = '1';

        // BR-02 fatal
        // An Invoice shall have an Invoice number (BT-1).

        // BR-03 fatal
        // An Invoice shall have an Invoice issue date (BT-2).

        // BR-04 fatal
        // An Invoice shall have an Invoice type code (BT-3).
        $c->namespaces[$inv]->elements['cbc:InvoiceTypeCode']->minOccurs = '1';
        $c->namespaces[$cre]->elements['cbc:InvoiceTypeCode']->minOccurs = '1';

        // BR-05 fatal
        // An Invoice shall have an Invoice currency code (BT-5).
        $c->namespaces[$inv]->elements['cbc:DocumentCurrencyCode']->minOccurs = '1';
        $c->namespaces[$cre]->elements['cbc:DocumentCurrencyCode']->minOccurs = '1';

        // BR-06 fatal
        // An Invoice shall contain the Seller name (BT-27).
        // BR-07 fatal
        // An Invoice shall contain the Buyer name (BT-44).
        $c->namespaces[$cac]->types['PartyLegalEntityType']->elements['cbc:RegistrationName']->minOccurs = '1';

        // BR-08 fatal
        // An Invoice shall contain the Seller postal address.
        // BR-10 fatal
        // An Invoice shall contain the Buyer postal address (BG-8).
        $c->namespaces[$cac]->types['PartyType']->elements['cac:PostalAddress']->minOccurs = '1';

        // BR-09 fatal
        // The Seller postal address (BG-5) shall contain a Seller country code (BT-40).
        // BR-11 fatal
        // The Buyer postal address shall contain a Buyer country code (BT-55).
        $c->namespaces[$cac]->types['CountryType']->elements['cbc:IdentificationCode']->minOccurs = '1';

        // BR-12 fatal
        // An Invoice shall have the Sum of Invoice line net amount (BT-106).
        $c->namespaces[$cac]->types['MonetaryTotalType']->elements['cbc:LineExtensionAmount']->minOccurs = '1';

        // BR-13 fatal
        // An Invoice shall have the Invoice total amount without VAT (BT-109).
        $c->namespaces[$cac]->types['MonetaryTotalType']->elements['cbc:TaxExclusiveAmount']->minOccurs = '1';

        // BR-14 fatal
        // An Invoice shall have the Invoice total amount with VAT (BT-112).
        $c->namespaces[$cac]->types['MonetaryTotalType']->elements['cbc:TaxInclusiveAmount']->minOccurs = '1';

        // BR-15 fatal
        // An Invoice shall have the Amount due for payment (BT-115).
        $c->namespaces[$cac]->types['MonetaryTotalType']->elements['cbc:PayableAmount']->minOccurs = '1';

        // BR-16 fatal
        // An Invoice shall have at least one Invoice line (BG-25)

        // BR-17 fatal
        // The Payee name (BT-59) shall be provided in the Invoice, if the Payee (BG-10) is different from the Seller (BG-4)

        // BR-18 fatal
        // The Seller tax representative name (BT-62) shall be provided in the Invoice, if the Seller (BG-4) has a Seller tax representative party (BG-11)

        // BR-19 fatal
        // The Seller tax representative postal address (BG-12) shall be provided in the Invoice, if the Seller (BG-4) has a Seller tax representative party (BG-11).

        // BR-20 fatal
        // The Seller tax representative postal address (BG-12) shall contain a Tax representative country code (BT-69), if the Seller (BG-4) has a Seller tax representative party (BG-11).

        // BR-21 fatal
        // Each Invoice line (BG-25) shall have an Invoice line identifier (BT-126).
        $c->namespaces[$cac]->types['InvoiceLineType']->elements['cbc:ID']->minOccurs = '1';
        $c->namespaces[$cac]->types['CreditNoteLineType']->elements['cbc:ID']->minOccurs = '1';

        // BR-22 fatal
        // Each Invoice line (BG-25) shall have an Invoiced quantity (BT-129).
        $c->namespaces[$cac]->types['InvoiceLineType']->elements['cbc:InvoicedQuantity']->minOccurs = '1';
        $c->namespaces[$cac]->types['CreditNoteLineType']->elements['cbc:CreditedQuantity']->minOccurs = '1';

        // BR-23 fatal
        // An Invoice line (BG-25) shall have an Invoiced quantity unit of measure code (BT-130).
        $c->namespaces[$cbc]->types['InvoicedQuantityType']->simpleContent->extension->attributes['unitCode']->use = 'required';
        $c->namespaces[$cac]->types['CreditedQuantityType']->simpleContent->extension->attributes['unitCode']->use = 'required';

        // BR-24 fatal
        // Each Invoice line (BG-25) shall have an Invoice line net amount (BT-131).
        $c->namespaces[$cac]->types['InvoiceLineType']->elements['cbc:LineExtensionAmount']->minOccurs = '1';
        $c->namespaces[$cac]->types['CreditNoteLineType']->elements['cbc:LineExtensionAmount']->minOccurs = '1';

        // BR-25 fatal
        // Each Invoice line (BG-25) shall contain the Item name (BT-153).
        $c->namespaces[$cac]->types['ItemType']->elements['cbc:name']->minOccurs = '1';

        // BR-26 fatal
        // Each Invoice line (BG-25) shall contain the Item net price (BT-146).
        $c->namespaces[$cac]->types['PriceType']->elements['cbc:PriceAmount']->minOccurs = '1';

        // BR-27 fatal
        // The Item net price (BT-146) shall NOT be negative.


        // BR-28 fatal
        // The Item gross price (BT-148) shall NOT be negative.


        // BR-29 fatal
        // If both Invoicing period start date (BT-73) and Invoicing period end date (BT-74) are given then the Invoicing period end date (BT-74) shall be later or equal to the Invoicing period start date (BT-73).


        // BR-30 fatal
        // If both Invoice line period start date (BT-134) and Invoice line period end date (BT-135) are given then the Invoice line period end date (BT-135) shall be later or equal to the Invoice line period start date (BT-134).


        // BR-31 fatal
        // Each Document level allowance (BG-20) shall have a Document level allowance amount (BT-92).
        $c->namespaces[$cac]->types['AllowanceChargeType']->elements['cbc:Amount']->minOccurs = '1';

        // BR-32 fatal
        // Each Document level allowance (BG-20) shall have a Document level allowance VAT category code (BT-95).
        $c->namespaces[$cac]->types['TaxCategoryType']->elements['cbc:ID']->minOccurs = '1';

        // BR-33 fatal
        // Each Document level allowance (BG-20) shall have a Document level allowance reason (BT-97) or a Document level allowance reason code (BT-98).


        // BR-36 fatal
        // Each Document level charge (BG-21) shall have a Document level charge amount (BT-99).


        // BR-37 fatal
        // Each Document level charge (BG-21) shall have a Document level charge VAT category code (BT-102).


        // BR-38 fatal
        // Each Document level charge (BG-21) shall have a Document level charge reason (BT-104) or a Document level charge reason code (BT-105).


        // BR-41 fatal
        // Each Invoice line allowance (BG-27) shall have an Invoice line allowance amount (BT-136).


        // BR-42 fatal
        // Each Invoice line allowance (BG-27) shall have an Invoice line allowance reason (BT-139) or an Invoice line allowance reason code (BT-140).


        // BR-43 fatal
        // Each Invoice line charge (BG-28) shall have an Invoice line charge amount (BT-141).


        // BR-44 fatal
        // Each Invoice line charge shall have an Invoice line charge reason or an invoice line allowance reason code.


        // BR-45 fatal
        // Each VAT breakdown (BG-23) shall have a VAT category taxable amount (BT-116).


        // BR-46 fatal
        // Each VAT breakdown (BG-23) shall have a VAT category tax amount (BT-117).


        // BR-47 fatal
        // Each VAT breakdown (BG-23) shall be defined through a VAT category code (BT-118).


        // BR-48 fatal
        // Each VAT breakdown (BG-23) shall have a VAT category rate (BT-119), except if the Invoice is not subject to VAT.


        // BR-49 fatal
        // A Payment instruction (BG-16) shall specify the Payment means type code (BT-81).


        // BR-50 fatal
        // A Payment account identifier (BT-84) shall be present if Credit transfer (BG-17) information is provided in the Invoice.


        // BR-51 warning
        // In accordance with card payments security standards an invoice should never include a full card primary account number (BT-87). At the moment PCI Security Standards Council has defined that the first 6 digits and last 4 digits are the maximum number of digits to be shown.


        // BR-52 fatal
        // Each Additional supporting document (BG-24) shall contain a Supporting document reference (BT-122).


        // BR-53 fatal
        // If the VAT accounting currency code (BT-6) is present, then the Invoice total VAT amount in accounting currency (BT-111) shall be provided.


        // BR-54 fatal
        // Each Item attribute (BG-32) shall contain an Item attribute name (BT-160) and an Item attribute value (BT-161).


        // BR-55 fatal
        // Each Preceding Invoice reference (BG-3) shall contain a Preceding Invoice reference (BT-25).


        // BR-56 fatal
        // Each Seller tax representative party (BG-11) shall have a Seller tax representative VAT identifier (BT-63).


        // BR-57 fatal
        // Each Deliver to address (BG-15) shall contain a Deliver to country code (BT-80).


        // BR-61 fatal
        // If the Payment means type code (BT-81) means SEPA credit transfer, Local credit transfer or Non-SEPA international credit transfer, the Payment account identifier (BT-84) shall be present.


        // BR-62 fatal
        // The Seller electronic address (BT-34) shall have a Scheme identifier.


        // BR-63 fatal
        // The Buyer electronic address (BT-49) shall have a Scheme identifier.


        // BR-64 fatal
        // The Item standard identifier (BT-157) shall have a Scheme identifier.


        // BR-65 fatal
        // The Item classification identifier (BT-158) shall have a Scheme identifier.


        // BR-AE-01 fatal
        // An Invoice that contains an Invoice line (BG-25), a Document level allowance (BG-20) or a Document level charge (BG-21) where the VAT category code (BT-151, BT-95 or BT-102) is "Reverse charge" shall contain in the VAT Breakdown (BG-23) exactly one VAT category code (BT-118) equal with "VAT reverse charge".


        // BR-AE-02 fatal
        // An Invoice that contains an Invoice line (BG-25) where the Invoiced item VAT category code (BT-151) is "Reverse charge" shall contain the Seller VAT Identifier (BT-31), the Seller Tax registration identifier (BT-32) and/or the Seller tax representative VAT identifier (BT-63) and the Buyer VAT identifier (BT-48) and/or the Buyer legal registration identifier (BT-47).


        // BR-AE-03 fatal
        // An Invoice that contains a Document level allowance (BG-20) where the Document level allowance VAT category code (BT-95) is "Reverse charge" shall contain the Seller VAT Identifier (BT-31), the Seller tax registration identifier (BT-32) and/or the Seller tax representative VAT identifier (BT-63) and the Buyer VAT identifier (BT-48) and/or the Buyer legal registration identifier (BT-47).


        // BR-AE-04 fatal
        // An Invoice that contains a Document level charge (BG-21) where the Document level charge VAT category code (BT-102) is "Reverse charge" shall contain the Seller VAT Identifier (BT-31), the Seller tax registration identifier (BT-32) and/or the Seller tax representative VAT identifier (BT-63) and the Buyer VAT identifier (BT-48) and/or the Buyer legal registration identifier (BT-47).


        // BR-AE-05 fatal
        // In an Invoice line (BG-25) where the Invoiced item VAT category code (BT-151) is "Reverse charge" the Invoiced item VAT rate (BT-152) shall be 0 (zero).


        // BR-AE-06 fatal
        // In a Document level allowance (BG-20) where the Document level allowance VAT category code (BT-95) is "Reverse charge" the Document level allowance VAT rate (BT-96) shall be 0 (zero).


        // BR-AE-07 fatal
        // In a Document level charge (BG-21) where the Document level charge VAT category code (BT-102) is "Reverse charge" the Document level charge VAT rate (BT-103) shall be 0 (zero).


        // BR-AE-08 fatal
        // In a VAT breakdown (BG-23) where the VAT category code (BT-118) is "Reverse charge" the VAT category taxable amount (BT-116) shall equal the sum of Invoice line net amounts (BT-131) minus the sum of Document level allowance amounts (BT-92) plus the sum of Document level charge amounts (BT-99) where the VAT category codes (BT-151, BT-95, BT-102) are "Reverse charge".


        // BR-AE-09 fatal
        // The VAT category tax amount (BT-117) in a VAT breakdown (BG-23) where the VAT category code (BT-118) is "Reverse charge" shall be 0 (zero).


        // BR-AE-10 fatal
        // A VAT breakdown (BG-23) with VAT Category code (BT-118) "Reverse charge" shall have a VAT exemption reason code (BT-121), meaning "Reverse charge" or the VAT exemption reason text (BT-120) "Reverse charge" (or the equivalent standard text in another language).


        // BR-AF-01 fatal
        // An Invoice that contains an Invoice line (BG-25), a Document level allowance (BG-20) or a Document level charge (BG-21) where the VAT category code (BT-151, BT-95 or BT-102) is "IGIC" shall contain in the VAT breakdown (BG-23) at least one VAT category code (BT-118) equal with "IGIC".


        // BR-AF-02 fatal
        // An Invoice that contains an Invoice line (BG-25) where the Invoiced item VAT category code (BT-151) is "IGIC" shall contain the Seller VAT Identifier (BT-31), the Seller tax registration identifier (BT-32) and/or the Seller tax representative VAT identifier (BT-63).


        // BR-AF-03 fatal
        // An Invoice that contains a Document level allowance (BG-20) where the Document level allowance VAT category code (BT-95) is "IGIC" shall contain the Seller VAT Identifier (BT-31), the Seller tax registration identifier (BT-32) and/or the Seller tax representative VAT identifier (BT-63).


        // BR-AF-04 fatal
        // An Invoice that contains a Document level charge (BG-21) where the Document level charge VAT category code (BT-102) is "IGIC" shall contain the Seller VAT Identifier (BT-31), the Seller Tax registration identifier (BT-32) and/or the Seller tax representative VAT identifier (BT-63).


        // BR-AF-05 fatal
        // In an Invoice line (BG-25) where the Invoiced item VAT category code (BT-151) is "IGIC" the invoiced item VAT rate (BT-152) shall be 0 (zero) or greater than zero.


        // BR-AF-06 fatal
        // In a Document level allowance (BG-20) where the Document level allowance VAT category code (BT-95) is "IGIC" the Document level allowance VAT rate (BT-96) shall be 0 (zero) or greater than zero.


        // BR-AF-07 fatal
        // In a Document level charge (BG-21) where the Document level charge VAT category code (BT-102) is "IGIC" the Document level charge VAT rate (BT-103) shall be 0 (zero) or greater than zero.


        // BR-AF-08 fatal
        // For each different value of VAT category rate (BT-119) where the VAT category code (BT-118) is "IGIC", the VAT category taxable amount (BT-116) in a VAT breakdown (BG-23) shall equal the sum of Invoice line net amounts (BT-131) plus the sum of document level charge amounts (BT-99) minus the sum of document level allowance amounts (BT-92) where the VAT category code (BT-151, BT-102, BT-95) is "IGIC" and the VAT rate (BT-152, BT-103, BT-96) equals the VAT category rate (BT-119).


        // BR-AF-09 fatal
        // The VAT category tax amount (BT-117) in a VAT breakdown (BG-23) where VAT category code (BT-118) is "IGIC" shall equal the VAT category taxable amount (BT-116) multiplied by the VAT category rate (BT-119).


        // BR-AF-10 fatal
        // A VAT breakdown (BG-23) with VAT Category code (BT-118) "IGIC" shall not have a VAT exemption reason code (BT-121) or VAT exemption reason text (BT-120).


        // BR-AG-01 fatal
        // An Invoice that contains an Invoice line (BG-25), a Document level allowance (BG-20) or a Document level charge (BG-21) where the VAT category code (BT-151, BT-95 or BT-102) is "IPSI" shall contain in the VAT breakdown (BG-23) at least one VAT category code (BT-118) equal with "IPSI".


        // BR-AG-02 fatal
        // An Invoice that contains an Invoice line (BG-25) where the Invoiced item VAT category code (BT-151) is "IPSI" shall contain the Seller VAT Identifier (BT-31), the Seller tax registration identifier (BT-32) and/or the Seller tax representative VAT identifier (BT-63).


        // BR-AG-03 fatal
        // An Invoice that contains a Document level allowance (BG-20) where the Document level allowance VAT category code (BT-95) is "IPSI" shall contain the Seller VAT Identifier (BT-31), the Seller Tax registration identifier (BT-32) and/or the Seller tax representative VAT identifier (BT-63).


        // BR-AG-04 fatal
        // An Invoice that contains a Document level charge (BG-21) where the Document level charge VAT category code (BT-102) is "IPSI" shall contain the Seller VAT Identifier (BT-31), the Seller Tax registration identifier (BT-32) and/or the Seller tax representative VAT identifier (BT-63).


        // BR-AG-05 fatal
        // In an Invoice line (BG-25) where the Invoiced item VAT category code (BT-151) is "IPSI" the Invoiced item VAT rate (BT-152) shall be 0 (zero) or greater than zero.


        // BR-AG-06 fatal
        // In a Document level allowance (BG-20) where the Document level allowance VAT category code (BT-95) is "IPSI" the Document level allowance VAT rate (BT-96) shall be 0 (zero) or greater than zero.


        // BR-AG-07 fatal
        // In a Document level charge (BG-21) where the Document level charge VAT category code (BT-102) is "IPSI" the Document level charge VAT rate (BT-103) shall be 0 (zero) or greater than zero.


        // BR-AG-08 fatal
        // For each different value of VAT category rate (BT-119) where the VAT category code (BT-118) is "IPSI", the VAT category taxable amount (BT-116) in a VAT breakdown (BG-23) shall equal the sum of Invoice line net amounts (BT-131) plus the sum of document level charge amounts (BT-99) minus the sum of document level allowance amounts (BT-92) where the VAT category code (BT-151, BT-102, BT-95) is "IPSI" and the VAT rate (BT-152, BT-103, BT-96) equals the VAT category rate (BT-119).


        // BR-AG-09 fatal
        // The VAT category tax amount (BT-117) in a VAT breakdown (BG-23) where VAT category code (BT-118) is "IPSI" shall equal the VAT category taxable amount (BT-116) multiplied by the VAT category rate (BT-119).


        // BR-AG-10 fatal
        // A VAT breakdown (BG-23) with VAT Category code (BT-118) "IPSI" shall not have a VAT exemption reason code (BT-121) or VAT exemption reason text (BT-120).


        // BR-B-01 fatal
        // An Invoice where the VAT category code (BT-151, BT-95 or BT-102) is “Split payment” shall be a domestic Italian invoice.


        // BR-B-02 fatal
        // An Invoice that contains an Invoice line (BG-25), a Document level allowance (BG-20) or a Document level charge (BG-21) where the VAT category code (BT-151, BT-95 or BT-102) is “Split payment" shall not contain an invoice line (BG-25), a Document level allowance (BG-20) or a Document level charge (BG-21) where the VAT category code (BT-151, BT-95 or BT-102) is “Standard rated”.


        // BR-CL-01 fatal
        // The document type code MUST be coded by the invoice and credit note related code lists of UNTDID 1001.


        // BR-CL-03 fatal
        // currencyID MUST be coded using ISO code list 4217 alpha-3


        // BR-CL-04 fatal
        // Invoice currency code MUST be coded using ISO code list 4217 alpha-3


        // BR-CL-05 fatal
        // Tax currency code MUST be coded using ISO code list 4217 alpha-3


        // BR-CL-06 fatal
        // Value added tax point date code MUST be coded using a restriction of UNTDID 2005.


        // BR-CL-07 fatal
        // Object identifier identification scheme identifier MUST be coded using a restriction of UNTDID 1153.


        // BR-CL-08 fatal
        // Invoiced note subject code shall be coded using UNCL4451


        // BR-CL-10 fatal
        // Any identifier identification scheme identifier MUST be coded using one of the ISO 6523 ICD list.


        // BR-CL-11 fatal
        // Any registration identifier identification scheme identifier MUST be coded using one of the ISO 6523 ICD list.


        // BR-CL-13 fatal
        // Item classification identifier identification scheme identifier MUST be coded using one of the UNTDID 7143 list.


        // BR-CL-14 fatal
        // Country codes in an invoice MUST be coded using ISO code list 3166-1


        // BR-CL-15 fatal
        // Country codes in an invoice MUST be coded using ISO code list 3166-1


        // BR-CL-16 fatal
        // Payment means in an invoice MUST be coded using UNCL4461 code list


        // BR-CL-17 fatal
        // Invoice tax categories MUST be coded using UNCL5305 code list


        // BR-CL-18 fatal
        // Invoice tax categories MUST be coded using UNCL5305 code list


        // BR-CL-19 fatal
        // Coded allowance reasons MUST belong to the UNCL 5189 code list


        // BR-CL-20 fatal
        // Coded charge reasons MUST belong to the UNCL 7161 code list


        // BR-CL-21 fatal
        // Item standard identifier scheme identifier MUST belong to the ISO 6523 ICD code list


        // BR-CL-22 fatal
        // Tax exemption reason code identifier scheme identifier MUST belong to the CEF VATEX code list


        // BR-CL-23 fatal
        // Unit code MUST be coded according to the UN/ECE Recommendation 20 with Rec 21 extension


        // BR-CL-24 fatal
        // For Mime code in attribute use MIMEMediaType.


        // BR-CL-25 fatal
        // Endpoint identifier scheme identifier MUST belong to the CEF EAS code list


        // BR-CL-26 fatal
        // Delivery location identifier scheme identifier MUST belong to the ISO 6523 ICD code list


        // BR-CO-03 fatal
        // Value added tax point date (BT-7) and Value added tax point date code (BT-8) are mutually exclusive.


        // BR-CO-04 fatal
        // Each Invoice line (BG-25) shall be categorized with an Invoiced item VAT category code (BT-151).


        // BR-CO-05 fatal
        // Document level allowance reason code (BT-98) and Document level allowance reason (BT-97) shall indicate the same type of allowance.


        // BR-CO-06 fatal
        // Document level charge reason code (BT-105) and Document level charge reason (BT-104) shall indicate the same type of charge.


        // BR-CO-07 fatal
        // Invoice line allowance reason code (BT-140) and Invoice line allowance reason (BT-139) shall indicate the same type of allowance reason.


        // BR-CO-08 fatal
        // Invoice line charge reason code (BT-145) and Invoice line charge reason (BT-144) shall indicate the same type of charge reason.


        // BR-CO-09 fatal
        // The Seller VAT identifier (BT-31), the Seller tax representative VAT identifier (BT-63) and the Buyer VAT identifier (BT-48) shall have a prefix in accordance with ISO code ISO 3166-1 alpha-2 by which the country of issue may be identified. Nevertheless, Greece may use the prefix ‘EL’.


        // BR-CO-10 fatal
        // Sum of Invoice line net amount (BT-106) = Σ Invoice line net amount (BT-131).


        // BR-CO-11 fatal
        // Sum of allowances on document level (BT-107) = Σ Document level allowance amount (BT-92).


        // BR-CO-12 fatal
        // Sum of charges on document level (BT-108) = Σ Document level charge amount (BT-99).


        // BR-CO-13 fatal
        // Invoice total amount without VAT (BT-109) = Σ Invoice line net amount (BT-131) - Sum of allowances on document level (BT-107) + Sum of charges on document level (BT-108).


        // BR-CO-14 fatal
        // Invoice total VAT amount (BT-110) = Σ VAT category tax amount (BT-117).


        // BR-CO-15 fatal
        // Invoice total amount with VAT (BT-112) = Invoice total amount without VAT (BT-109) + Invoice total VAT amount (BT-110).


        // BR-CO-16 fatal
        // Amount due for payment (BT-115) = Invoice total amount with VAT (BT-112) -Paid amount (BT-113) +Rounding amount (BT-114).


        // BR-CO-17 fatal
        // VAT category tax amount (BT-117) = VAT category taxable amount (BT-116) x (VAT category rate (BT-119) / 100), rounded to two decimals.


        // BR-CO-18 fatal
        // An Invoice shall at least have one VAT breakdown group (BG-23).


        // BR-CO-19 fatal
        // If Invoicing period (BG-14) is used, the Invoicing period start date (BT-73) or the Invoicing period end date (BT-74) shall be filled, or both.


        // BR-CO-20 fatal
        // If Invoice line period (BG-26) is used, the Invoice line period start date (BT-134) or the Invoice line period end date (BT-135) shall be filled, or both.


        // BR-CO-21 fatal
        // Each Document level allowance (BG-20) shall contain a Document level allowance reason (BT-97) or a Document level allowance reason code (BT-98), or both.


        // BR-CO-22 fatal
        // Each Document level charge (BG-21) shall contain a Document level charge reason (BT-104) or a Document level charge reason code (BT-105), or both.


        // BR-CO-23 fatal
        // Each Invoice line allowance (BG-27) shall contain an Invoice line allowance reason (BT-139) or an Invoice line allowance reason code (BT-140), or both.


        // BR-CO-24 fatal
        // Each Invoice line charge (BG-28) shall contain an Invoice line charge reason (BT-144) or an Invoice line charge reason code (BT-145), or both.


        // BR-CO-25 fatal
        // In case the Amount due for payment (BT-115) is positive, either the Payment due date (BT-9) or the Payment terms (BT-20) shall be present.


        // BR-CO-26 fatal
        // In order for the buyer to automatically identify a supplier, the Seller identifier (BT-29), the Seller legal registration identifier (BT-30) and/or the Seller VAT identifier (BT-31) shall be present.


        // BR-DEC-01 fatal
        // The allowed maximum number of decimals for the Document level allowance amount (BT-92) is 2.


        // BR-DEC-02 fatal
        // The allowed maximum number of decimals for the Document level allowance base amount (BT-93) is 2.


        // BR-DEC-05 fatal
        // The allowed maximum number of decimals for the Document level charge amount (BT-99) is 2.


        // BR-DEC-06 fatal
        // The allowed maximum number of decimals for the Document level charge base amount (BT-100) is 2.


        // BR-DEC-09 fatal
        // The allowed maximum number of decimals for the Sum of Invoice line net amount (BT-106) is 2.


        // BR-DEC-10 fatal
        // The allowed maximum number of decimals for the Sum of allowanced on document level (BT-107) is 2.


        // BR-DEC-11 fatal
        // The allowed maximum number of decimals for the Sum of charges on document level (BT-108) is 2.


        // BR-DEC-12 fatal
        // The allowed maximum number of decimals for the Invoice total amount without VAT (BT-109) is 2.


        // BR-DEC-13 fatal
        // The allowed maximum number of decimals for the Invoice total VAT amount (BT-110) is 2.


        // BR-DEC-14 fatal
        // The allowed maximum number of decimals for the Invoice total amount with VAT (BT-112) is 2.


        // BR-DEC-15 fatal
        // The allowed maximum number of decimals for the Invoice total VAT amount in accounting currency (BT-111) is 2.


        // BR-DEC-16 fatal
        // The allowed maximum number of decimals for the Paid amount (BT-113) is 2.


        // BR-DEC-17 fatal
        // The allowed maximum number of decimals for the Rounding amount (BT-114) is 2.


        // BR-DEC-18 fatal
        // The allowed maximum number of decimals for the Amount due for payment (BT-115) is 2.


        // BR-DEC-19 fatal
        // The allowed maximum number of decimals for the VAT category taxable amount (BT-116) is 2.


        // BR-DEC-20 fatal
        // The allowed maximum number of decimals for the VAT category tax amount (BT-117) is 2.


        // BR-DEC-23 fatal
        // The allowed maximum number of decimals for the Invoice line net amount (BT-131) is 2.


        // BR-DEC-24 fatal
        // The allowed maximum number of decimals for the Invoice line allowance amount (BT-136) is 2.


        // BR-DEC-25 fatal
        // The allowed maximum number of decimals for the Invoice line allowance base amount (BT-137) is 2.


        // BR-DEC-27 fatal
        // The allowed maximum number of decimals for the Invoice line charge amount (BT-141) is 2.


        // BR-DEC-28 fatal
        // The allowed maximum number of decimals for the Invoice line charge base amount (BT-142) is 2.


        // BR-E-01 fatal
        // An Invoice that contains an Invoice line (BG-25), a Document level allowance (BG-20) or a Document level charge (BG-21) where the VAT category code (BT-151, BT-95 or BT-102) is "Exempt from VAT" shall contain exactly one VAT breakdown (BG-23) with the VAT category code (BT-118) equal to "Exempt from VAT".


        // BR-E-02 fatal
        // An Invoice that contains an Invoice line (BG-25) where the Invoiced item VAT category code (BT-151) is "Exempt from VAT" shall contain the Seller VAT Identifier (BT-31), the Seller tax registration identifier (BT-32) and/or the Seller tax representative VAT identifier (BT-63).


        // BR-E-03 fatal
        // An Invoice that contains a Document level allowance (BG-20) where the Document level allowance VAT category code (BT-95) is "Exempt from VAT" shall contain the Seller VAT Identifier (BT-31), the Seller tax registration identifier (BT-32) and/or the Seller tax representative VAT identifier (BT-63).


        // BR-E-04 fatal
        // An Invoice that contains a Document level charge (BG-21) where the Document level charge VAT category code (BT-102) is "Exempt from VAT" shall contain the Seller VAT Identifier (BT-31), the Seller tax registration identifier (BT-32) and/or the Seller tax representative VAT identifier (BT-63).


        // BR-E-05 fatal
        // In an Invoice line (BG-25) where the Invoiced item VAT category code (BT-151) is "Exempt from VAT", the Invoiced item VAT rate (BT-152) shall be 0 (zero).


        // BR-E-06 fatal
        // In a Document level allowance (BG-20) where the Document level allowance VAT category code (BT-95) is "Exempt from VAT", the Document level allowance VAT rate (BT-96) shall be 0 (zero).


        // BR-E-07 fatal
        // In a Document level charge (BG-21) where the Document level charge VAT category code (BT-102) is "Exempt from VAT", the Document level charge VAT rate (BT-103) shall be 0 (zero).


        // BR-E-08 fatal
        // In a VAT breakdown (BG-23) where the VAT category code (BT-118) is "Exempt from VAT" the VAT category taxable amount (BT-116) shall equal the sum of Invoice line net amounts (BT-131) minus the sum of Document level allowance amounts (BT-92) plus the sum of Document level charge amounts (BT-99) where the VAT category codes (BT-151, BT-95, BT-102) are "Exempt from VAT".


        // BR-E-09 fatal
        // The VAT category tax amount (BT-117) In a VAT breakdown (BG-23) where the VAT category code (BT-118) equals "Exempt from VAT" shall equal 0 (zero).


        // BR-E-10 fatal
        // A VAT breakdown (BG-23) with VAT Category code (BT-118) "Exempt from VAT" shall have a VAT exemption reason code (BT-121) or a VAT exemption reason text (BT-120).


        // BR-G-01 fatal
        // An Invoice that contains an Invoice line (BG-25), a Document level allowance (BG-20) or a Document level charge (BG-21) where the VAT category code (BT-151, BT-95 or BT-102) is "Export outside the EU" shall contain in the VAT breakdown (BG-23) exactly one VAT category code (BT-118) equal with "Export outside the EU".


        // BR-G-02 fatal
        // An Invoice that contains an Invoice line (BG-25) where the Invoiced item VAT category code (BT-151) is "Export outside the EU" shall contain the Seller VAT Identifier (BT-31) or the Seller tax representative VAT identifier (BT-63).


        // BR-G-03 fatal
        // An Invoice that contains a Document level allowance (BG-20) where the Document level allowance VAT category code (BT-95) is "Export outside the EU" shall contain the Seller VAT Identifier (BT-31) or the Seller tax representative VAT identifier (BT-63).


        // BR-G-04 fatal
        // An Invoice that contains a Document level charge (BG-21) where the Document level charge VAT category code (BT-102) is "Export outside the EU" shall contain the Seller VAT Identifier (BT-31) or the Seller tax representative VAT identifier (BT-63).


        // BR-G-05 fatal
        // In an Invoice line (BG-25) where the Invoiced item VAT category code (BT-151) is "Export outside the EU" the Invoiced item VAT rate (BT-152) shall be 0 (zero).


        // BR-G-06 fatal
        // In a Document level allowance (BG-20) where the Document level allowance VAT category code (BT-95) is "Export outside the EU" the Document level allowance VAT rate (BT-96) shall be 0 (zero).


        // BR-G-07 fatal
        // In a Document level charge (BG-21) where the Document level charge VAT category code (BT-102) is "Export outside the EU" the Document level charge VAT rate (BT-103) shall be 0 (zero).


        // BR-G-08 fatal
        // In a VAT breakdown (BG-23) where the VAT category code (BT-118) is "Export outside the EU" the VAT category taxable amount (BT-116) shall equal the sum of Invoice line net amounts (BT-131) minus the sum of Document level allowance amounts (BT-92) plus the sum of Document level charge amounts (BT-99) where the VAT category codes (BT-151, BT-95, BT-102) are "Export outside the EU".


        // BR-G-09 fatal
        // The VAT category tax amount (BT-117) in a VAT breakdown (BG-23) where the VAT category code (BT-118) is "Export outside the EU" shall be 0 (zero).


        // BR-G-10 fatal
        // A VAT breakdown (BG-23) with the VAT Category code (BT-118) "Export outside the EU" shall have a VAT exemption reason code (BT-121), meaning "Export outside the EU" or the VAT exemption reason text (BT-120) "Export outside the EU" (or the equivalent standard text in another language).


        // BR-IC-01 fatal
        // An Invoice that contains an Invoice line (BG-25), a Document level allowance (BG-20) or a Document level charge (BG-21) where the VAT category code (BT-151, BT-95 or BT-102) is "Intra-community supply" shall contain in the VAT breakdown (BG-23) exactly one VAT category code (BT-118) equal with "Intra-community supply".


        // BR-IC-02 fatal
        // An Invoice that contains an Invoice line (BG-25) where the Invoiced item VAT category code (BT-151) is "Intra-community supply" shall contain the Seller VAT Identifier (BT-31) or the Seller tax representative VAT identifier (BT-63) and the Buyer VAT identifier (BT-48).


        // BR-IC-03 fatal
        // An Invoice that contains a Document level allowance (BG-20) where the Document level allowance VAT category code (BT-95) is "Intra-community supply" shall contain the Seller VAT Identifier (BT-31) or the Seller tax representative VAT identifier (BT-63) and the Buyer VAT identifier (BT-48).


        // BR-IC-04 fatal
        // An Invoice that contains a Document level charge (BG-21) where the Document level charge VAT category code (BT-102) is "Intra-community supply" shall contain the Seller VAT Identifier (BT-31) or the Seller tax representative VAT identifier (BT-63) and the Buyer VAT identifier (BT-48).


        // BR-IC-05 fatal
        // In an Invoice line (BG-25) where the Invoiced item VAT category code (BT-151) is "Intracommunity supply" the Invoiced item VAT rate (BT-152) shall be 0 (zero).


        // BR-IC-06 fatal
        // In a Document level allowance (BG-20) where the Document level allowance VAT category code (BT-95) is "Intra-community supply" the Document level allowance VAT rate (BT-96) shall be 0 (zero).


        // BR-IC-07 fatal
        // In a Document level charge (BG-21) where the Document level charge VAT category code (BT-102) is "Intra-community supply" the Document level charge VAT rate (BT-103) shall be 0 (zero).


        // BR-IC-08 fatal
        // In a VAT breakdown (BG-23) where the VAT category code (BT-118) is "Intra-community supply" the VAT category taxable amount (BT-116) shall equal the sum of Invoice line net amounts (BT-131) minus the sum of Document level allowance amounts (BT-92) plus the sum of Document level charge amounts (BT-99) where the VAT category codes (BT-151, BT-95, BT-102) are "Intra-community supply".


        // BR-IC-09 fatal
        // The VAT category tax amount (BT-117) in a VAT breakdown (BG-23) where the VAT category code (BT-118) is "Intra-community supply" shall be 0 (zero).


        // BR-IC-10 fatal
        // A VAT breakdown (BG-23) with the VAT Category code (BT-118) "Intra-community supply" shall have a VAT exemption reason code (BT-121), meaning "Intra-community supply" or the VAT exemption reason text (BT-120) "Intra-community supply" (or the equivalent standard text in another language).


        // BR-IC-11 fatal
        // In an Invoice with a VAT breakdown (BG-23) where the VAT category code (BT-118) is "Intra-community supply" the Actual delivery date (BT-72) or the Invoicing period (BG-14) shall not be blank.


        // BR-IC-12 fatal
        // In an Invoice with a VAT breakdown (BG-23) where the VAT category code (BT-118) is "Intra-community supply" the Deliver to country code (BT-80) shall not be blank.


        // BR-O-01 fatal
        // An Invoice that contains an Invoice line (BG-25), a Document level allowance (BG-20) or a Document level charge (BG-21) where the VAT category code (BT-151, BT-95 or BT-102) is "Not subject to VAT" shall contain exactly one VAT breakdown group (BG-23) with the VAT category code (BT-118) equal to "Not subject to VAT".


        // BR-O-02 fatal
        // An Invoice that contains an Invoice line (BG-25) where the Invoiced item VAT category code (BT-151) is "Not subject to VAT" shall not contain the Seller VAT identifier (BT-31), the Seller tax representative VAT identifier (BT-63) or the Buyer VAT identifier (BT-48).


        // BR-O-03 fatal
        // An Invoice that contains a Document level allowance (BG-20) where the Document level allowance VAT category code (BT-95) is "Not subject to VAT" shall not contain the Seller VAT identifier (BT-31), the Seller tax representative VAT identifier (BT-63) or the Buyer VAT identifier (BT-48).


        // BR-O-04 fatal
        // An Invoice that contains a Document level charge (BG-21) where the Document level charge VAT category code (BT-102) is "Not subject to VAT" shall not contain the Seller VAT identifier (BT-31), the Seller tax representative VAT identifier (BT-63) or the Buyer VAT identifier (BT-48).


        // BR-O-05 fatal
        // An Invoice line (BG-25) where the VAT category code (BT-151) is "Not subject to VAT" shall not contain an Invoiced item VAT rate (BT-152).


        // BR-O-06 fatal
        // A Document level allowance (BG-20) where VAT category code (BT-95) is "Not subject to VAT" shall not contain a Document level allowance VAT rate (BT-96).


        // BR-O-07 fatal
        // A Document level charge (BG-21) where the VAT category code (BT-102) is "Not subject to VAT" shall not contain a Document level charge VAT rate (BT-103).


        // BR-O-08 fatal
        // In a VAT breakdown (BG-23) where the VAT category code (BT-118) is " Not subject to VAT" the VAT category taxable amount (BT-116) shall equal the sum of Invoice line net amounts (BT-131) minus the sum of Document level allowance amounts (BT-92) plus the sum of Document level charge amounts (BT-99) where the VAT category codes (BT-151, BT-95, BT-102) are "Not subject to VAT".


        // BR-O-09 fatal
        // The VAT category tax amount (BT-117) in a VAT breakdown (BG-23) where the VAT category code (BT-118) is "Not subject to VAT" shall be 0 (zero).


        // BR-O-10 fatal
        // A VAT breakdown (BG-23) with VAT Category code (BT-118) " Not subject to VAT" shall have a VAT exemption reason code (BT-121), meaning " Not subject to VAT" or a VAT exemption reason text (BT-120) " Not subject to VAT" (or the equivalent standard text in another language).


        // BR-O-11 fatal
        // An Invoice that contains a VAT breakdown group (BG-23) with a VAT category code (BT-118) "Not subject to VAT" shall not contain other VAT breakdown groups (BG-23).


        // BR-O-12 fatal
        // An Invoice that contains a VAT breakdown group (BG-23) with a VAT category code (BT-118) "Not subject to VAT" shall not contain an Invoice line (BG-25) where the Invoiced item VAT category code (BT-151) is not "Not subject to VAT".


        // BR-O-13 fatal
        // An Invoice that contains a VAT breakdown group (BG-23) with a VAT category code (BT-118) "Not subject to VAT" shall not contain Document level allowances (BG-20) where Document level allowance VAT category code (BT-95) is not "Not subject to VAT".


        // BR-O-14 fatal
        // An Invoice that contains a VAT breakdown group (BG-23) with a VAT category code (BT-118) "Not subject to VAT" shall not contain Document level charges (BG-21) where Document level charge VAT category code (BT-102) is not "Not subject to VAT".


        // BR-S-01 fatal
        // An Invoice that contains an Invoice line (BG-25), a Document level allowance (BG-20) or a Document level charge (BG-21) where the VAT category code (BT-151, BT-95 or BT-102) is "Standard rated" shall contain in the VAT breakdown (BG-23) at least one VAT category code (BT-118) equal with "Standard rated".


        // BR-S-02 fatal
        // An Invoice that contains an Invoice line (BG-25) where the Invoiced item VAT category code (BT-151) is "Standard rated" shall contain the Seller VAT Identifier (BT-31), the Seller tax registration identifier (BT-32) and/or the Seller tax representative VAT identifier (BT-63).


        // BR-S-03 fatal
        // An Invoice that contains a Document level allowance (BG-20) where the Document level allowance VAT category code (BT-95) is "Standard rated" shall contain the Seller VAT Identifier (BT-31), the Seller tax registration identifier (BT-32) and/or the Seller tax representative VAT identifier (BT-63).


        // BR-S-04 fatal
        // An Invoice that contains a Document level charge (BG-21) where the Document level charge VAT category code (BT-102) is "Standard rated" shall contain the Seller VAT Identifier (BT-31), the Seller tax registration identifier (BT-32) and/or the Seller tax representative VAT identifier (BT-63).


        // BR-S-05 fatal
        // In an Invoice line (BG-25) where the Invoiced item VAT category code (BT-151) is "Standard rated" the Invoiced item VAT rate (BT-152) shall be greater than zero.


        // BR-S-06 fatal
        // In a Document level allowance (BG-20) where the Document level allowance VAT category code (BT-95) is "Standard rated" the Document level allowance VAT rate (BT-96) shall be greater than zero.


        // BR-S-07 fatal
        // In a Document level charge (BG-21) where the Document level charge VAT category code (BT-102) is "Standard rated" the Document level charge VAT rate (BT-103) shall be greater than zero.


        // BR-S-08 fatal
        // For each different value of VAT category rate (BT-119) where the VAT category code (BT-118) is "Standard rated", the VAT category taxable amount (BT-116) in a VAT breakdown (BG-23) shall equal the sum of Invoice line net amounts (BT-131) plus the sum of document level charge amounts (BT-99) minus the sum of document level allowance amounts (BT-92) where the VAT category code (BT-151, BT-102, BT-95) is "Standard rated" and the VAT rate (BT-152, BT-103, BT-96) equals the VAT category rate (BT-119).


        // BR-S-09 fatal
        // The VAT category tax amount (BT-117) in a VAT breakdown (BG-23) where VAT category code (BT-118) is "Standard rated" shall equal the VAT category taxable amount (BT-116) multiplied by the VAT category rate (BT-119).


        // BR-S-10 fatal
        // A VAT breakdown (BG-23) with VAT Category code (BT-118) "Standard rate" shall not have a VAT exemption reason code (BT-121) or VAT exemption reason text (BT-120).


        // BR-Z-01 fatal
        // An Invoice that contains an Invoice line (BG-25), a Document level allowance (BG-20) or a Document level charge (BG-21) where the VAT category code (BT-151, BT-95 or BT-102) is "Zero rated" shall contain in the VAT breakdown (BG-23) exactly one VAT category code (BT-118) equal with "Zero rated".


        // BR-Z-02 fatal
        // An Invoice that contains an Invoice line where the Invoiced item VAT category code (BT-151) is "Zero rated" shall contain the Seller VAT Identifier (BT-31), the Seller tax registration identifier (BT-32) and/or the Seller tax representative VAT identifier (BT-63).


        // BR-Z-03 fatal
        // An Invoice that contains a Document level allowance (BG-20) where the Document level allowance VAT category code (BT-95) is "Zero rated" shall contain the Seller VAT Identifier (BT-31), the Seller tax registration identifier (BT-32) and/or the Seller tax representative VAT identifier (BT-63).


        // BR-Z-04 fatal
        // An Invoice that contains a Document level charge where the Document level charge VAT category code (BT-102) is "Zero rated" shall contain the Seller VAT Identifier (BT-31), the Seller tax registration identifier (BT-32) and/or the Seller tax representative VAT identifier (BT-63).


        // BR-Z-05 fatal
        // In an Invoice line (BG-25) where the Invoiced item VAT category code (BT-151) is "Zero rated" the Invoiced item VAT rate (BT-152) shall be 0 (zero).


        // BR-Z-06 fatal
        // In a Document level allowance (BG-20) where the Document level allowance VAT category code (BT-95) is "Zero rated" the Document level allowance VAT rate (BT-96) shall be 0 (zero).


        // BR-Z-07 fatal
        // In a Document level charge (BG-21) where the Document level charge VAT category code (BT-102) is "Zero rated" the Document level charge VAT rate (BT-103) shall be 0 (zero).


        // BR-Z-08 fatal
        // In a VAT breakdown (BG-23) where VAT category code (BT-118) is "Zero rated" the VAT category taxable amount (BT-116) shall equal the sum of Invoice line net amount (BT-131) minus the sum of Document level allowance amounts (BT-92) plus the sum of Document level charge amounts (BT-99) where the VAT category codes (BT-151, BT-95, BT-102) are "Zero rated".


        // BR-Z-09 fatal
        // The VAT category tax amount (BT-117) in a VAT breakdown (BG-23) where VAT category code (BT-118) is "Zero rated" shall equal 0 (zero).


        // BR-Z-10 fatal
        // A VAT breakdown (BG-23) with VAT Category code (BT-118) "Zero rated" shall not have a VAT exemption reason code (BT-121) or VAT exemption reason text (BT-120).


        // UBL-CR-001 warning
        // A UBL invoice should not include extensions


        // UBL-CR-002 warning
        // A UBL invoice should not include the UBLVersionID or it should be 2.1


        // UBL-CR-003 warning
        // A UBL invoice should not include the ProfileExecutionID


        // UBL-CR-004 warning
        // A UBL invoice should not include the CopyIndicator


        // UBL-CR-005 warning
        // A UBL invoice should not include the UUID


        // UBL-CR-006 warning
        // A UBL invoice should not include the IssueTime


        // UBL-CR-007 warning
        // A UBL invoice should not include the PricingCurrencyCode


        // UBL-CR-008 warning
        // A UBL invoice should not include the PaymentCurrencyCode


        // UBL-CR-009 warning
        // A UBL invoice should not include the PaymentAlternativeCurrencyCode


        // UBL-CR-010 warning
        // A UBL invoice should not include the AccountingCostCode


        // UBL-CR-011 warning
        // A UBL invoice should not include the LineCountNumeric


        // UBL-CR-012 warning
        // A UBL invoice should not include the InvoicePeriod StartTime


        // UBL-CR-013 warning
        // A UBL invoice should not include the InvoicePeriod EndTime


        // UBL-CR-014 warning
        // A UBL invoice should not include the InvoicePeriod DurationMeasure


        // UBL-CR-015 warning
        // A UBL invoice should not include the InvoicePeriod Description


        // UBL-CR-016 warning
        // A UBL invoice should not include the OrderReference CopyIndicator


        // UBL-CR-017 warning
        // A UBL invoice should not include the OrderReference UUID


        // UBL-CR-018 warning
        // A UBL invoice should not include the OrderReference IssueDate


        // UBL-CR-019 warning
        // A UBL invoice should not include the OrderReference IssueTime


        // UBL-CR-020 warning
        // A UBL invoice should not include the OrderReference CustomerReference


        // UBL-CR-021 warning
        // A UBL invoice should not include the OrderReference OrderTypeCode


        // UBL-CR-022 warning
        // A UBL invoice should not include the OrderReference DocumentReference


        // UBL-CR-023 warning
        // A UBL invoice should not include the BillingReference CopyIndicator


        // UBL-CR-024 warning
        // A UBL invoice should not include the BillingReference UUID


        // UBL-CR-025 warning
        // A UBL invoice should not include the BillingReference IssueTime


        // UBL-CR-026 warning
        // A UBL invoice should not include the BillingReference DocumentTypeCode


        // UBL-CR-027 warning
        // A UBL invoice should not include the BillingReference DocumentType


        // UBL-CR-028 warning
        // A UBL invoice should not include the BillingReference Xpath


        // UBL-CR-029 warning
        // A UBL invoice should not include the BillingReference LanguageID


        // UBL-CR-030 warning
        // A UBL invoice should not include the BillingReference LocaleCode


        // UBL-CR-031 warning
        // A UBL invoice should not include the BillingReference VersionID


        // UBL-CR-032 warning
        // A UBL invoice should not include the BillingReference DocumentStatusCode


        // UBL-CR-033 warning
        // A UBL invoice should not include the BillingReference DocumenDescription


        // UBL-CR-034 warning
        // A UBL invoice should not include the BillingReference Attachment


        // UBL-CR-035 warning
        // A UBL invoice should not include the BillingReference ValidityPeriod


        // UBL-CR-036 warning
        // A UBL invoice should not include the BillingReference IssuerParty


        // UBL-CR-037 warning
        // A UBL invoice should not include the BillingReference ResultOfVerification


        // UBL-CR-038 warning
        // A UBL invoice should not include the BillingReference SelfBilledInvoiceDocumentReference


        // UBL-CR-039 warning
        // A UBL invoice should not include the BillingReference CreditNoteDocumentReference


        // UBL-CR-040 warning
        // A UBL invoice should not include the BillingReference SelfBilledCreditNoteDocumentReference


        // UBL-CR-041 warning
        // A UBL invoice should not include the BillingReference DebitNoteDocumentReference


        // UBL-CR-042 warning
        // A UBL invoice should not include the BillingReference ReminderDocumentReference


        // UBL-CR-043 warning
        // A UBL invoice should not include the BillingReference AdditionalDocumentReference


        // UBL-CR-044 warning
        // A UBL invoice should not include the BillingReference BillingReferenceLine


        // UBL-CR-045 warning
        // A UBL invoice should not include the DespatchDocumentReference CopyIndicator


        // UBL-CR-046 warning
        // A UBL invoice should not include the DespatchDocumentReference UUID


        // UBL-CR-047 warning
        // A UBL invoice should not include the DespatchDocumentReference IssueDate


        // UBL-CR-048 warning
        // A UBL invoice should not include the DespatchDocumentReference IssueTime


        // UBL-CR-049 warning
        // A UBL invoice should not include the DespatchDocumentReference DocumentTypeCode


        // UBL-CR-050 warning
        // A UBL invoice should not include the DespatchDocumentReference DocumentType


        // UBL-CR-051 warning
        // A UBL invoice should not include the DespatchDocumentReference Xpath


        // UBL-CR-052 warning
        // A UBL invoice should not include the DespatchDocumentReference LanguageID


        // UBL-CR-053 warning
        // A UBL invoice should not include the DespatchDocumentReference LocaleCode


        // UBL-CR-054 warning
        // A UBL invoice should not include the DespatchDocumentReference VersionID


        // UBL-CR-055 warning
        // A UBL invoice should not include the DespatchDocumentReference DocumentStatusCode


        // UBL-CR-056 warning
        // A UBL invoice should not include the DespatchDocumentReference DocumentDescription


        // UBL-CR-057 warning
        // A UBL invoice should not include the DespatchDocumentReference Attachment


        // UBL-CR-058 warning
        // A UBL invoice should not include the DespatchDocumentReference ValidityPeriod


        // UBL-CR-059 warning
        // A UBL invoice should not include the DespatchDocumentReference IssuerParty


        // UBL-CR-060 warning
        // A UBL invoice should not include the DespatchDocumentReference ResultOfVerification


        // UBL-CR-061 warning
        // A UBL invoice should not include the ReceiptDocumentReference CopyIndicator


        // UBL-CR-062 warning
        // A UBL invoice should not include the ReceiptDocumentReference UUID


        // UBL-CR-063 warning
        // A UBL invoice should not include the ReceiptDocumentReference IssueDate


        // UBL-CR-064 warning
        // A UBL invoice should not include the ReceiptDocumentReference IssueTime


        // UBL-CR-065 warning
        // A UBL invoice should not include the ReceiptDocumentReference DocumentTypeCode


        // UBL-CR-066 warning
        // A UBL invoice should not include the ReceiptDocumentReference DocumentType


        // UBL-CR-067 warning
        // A UBL invoice should not include the ReceiptDocumentReference Xpath


        // UBL-CR-068 warning
        // A UBL invoice should not include the ReceiptDocumentReference LanguageID


        // UBL-CR-069 warning
        // A UBL invoice should not include the ReceiptDocumentReference LocaleCode


        // UBL-CR-070 warning
        // A UBL invoice should not include the ReceiptDocumentReference VersionID


        // UBL-CR-071 warning
        // A UBL invoice should not include the ReceiptDocumentReference DocumentStatusCode


        // UBL-CR-072 warning
        // A UBL invoice should not include the ReceiptDocumentReference DocumentDescription


        // UBL-CR-073 warning
        // A UBL invoice should not include the ReceiptDocumentReference Attachment


        // UBL-CR-074 warning
        // A UBL invoice should not include the ReceiptDocumentReference ValidityPeriod


        // UBL-CR-075 warning
        // A UBL invoice should not include the ReceiptDocumentReference IssuerParty


        // UBL-CR-076 warning
        // A UBL invoice should not include the ReceiptDocumentReference ResultOfVerification


        // UBL-CR-077 warning
        // A UBL invoice should not include the StatementDocumentReference


        // UBL-CR-078 warning
        // A UBL invoice should not include the OriginatorDocumentReference CopyIndicator


        // UBL-CR-079 warning
        // A UBL invoice should not include the OriginatorDocumentReference UUID


        // UBL-CR-080 warning
        // A UBL invoice should not include the OriginatorDocumentReference IssueDate


        // UBL-CR-081 warning
        // A UBL invoice should not include the OriginatorDocumentReference IssueTime


        // UBL-CR-082 warning
        // A UBL invoice should not include the OriginatorDocumentReference DocumentTypeCode


        // UBL-CR-083 warning
        // A UBL invoice should not include the OriginatorDocumentReference DocumentType


        // UBL-CR-084 warning
        // A UBL invoice should not include the OriginatorDocumentReference Xpath


        // UBL-CR-085 warning
        // A UBL invoice should not include the OriginatorDocumentReference LanguageID


        // UBL-CR-086 warning
        // A UBL invoice should not include the OriginatorDocumentReference LocaleCode


        // UBL-CR-087 warning
        // A UBL invoice should not include the OriginatorDocumentReference VersionID


        // UBL-CR-088 warning
        // A UBL invoice should not include the OriginatorDocumentReference DocumentStatusCode


        // UBL-CR-089 warning
        // A UBL invoice should not include the OriginatorDocumentReference DocumentDescription


        // UBL-CR-090 warning
        // A UBL invoice should not include the OriginatorDocumentReference Attachment


        // UBL-CR-091 warning
        // A UBL invoice should not include the OriginatorDocumentReference ValidityPeriod


        // UBL-CR-092 warning
        // A UBL invoice should not include the OriginatorDocumentReference IssuerParty


        // UBL-CR-093 warning
        // A UBL invoice should not include the OriginatorDocumentReference ResultOfVerification


        // UBL-CR-094 warning
        // A UBL invoice should not include the ContractDocumentReference CopyIndicator


        // UBL-CR-095 warning
        // A UBL invoice should not include the ContractDocumentReference UUID


        // UBL-CR-096 warning
        // A UBL invoice should not include the ContractDocumentReference IssueDate


        // UBL-CR-097 warning
        // A UBL invoice should not include the ContractDocumentReference IssueTime


        // UBL-CR-098 warning
        // A UBL invoice should not include the ContractDocumentReference DocumentTypeCode


        // UBL-CR-099 warning
        // A UBL invoice should not include the ContractDocumentReference DocumentType


        // UBL-CR-100 warning
        // A UBL invoice should not include the ContractDocumentReference Xpath


        // UBL-CR-101 warning
        // A UBL invoice should not include the ContractDocumentReference LanguageID


        // UBL-CR-102 warning
        // A UBL invoice should not include the ContractDocumentReference LocaleCode


        // UBL-CR-103 warning
        // A UBL invoice should not include the ContractDocumentReference VersionID


        // UBL-CR-104 warning
        // A UBL invoice should not include the ContractDocumentReference DocumentStatusCode


        // UBL-CR-105 warning
        // A UBL invoice should not include the ContractDocumentReference DocumentDescription


        // UBL-CR-106 warning
        // A UBL invoice should not include the ContractDocumentReference Attachment


        // UBL-CR-107 warning
        // A UBL invoice should not include the ContractDocumentReference ValidityPeriod


        // UBL-CR-108 warning
        // A UBL invoice should not include the ContractDocumentReference IssuerParty


        // UBL-CR-109 warning
        // A UBL invoice should not include the ContractDocumentReference ResultOfVerification


        // UBL-CR-110 warning
        // A UBL invoice should not include the AdditionalDocumentReference CopyIndicator


        // UBL-CR-111 warning
        // A UBL invoice should not include the AdditionalDocumentReference UUID


        // UBL-CR-112 warning
        // A UBL invoice should not include the AdditionalDocumentReference IssueDate


        // UBL-CR-113 warning
        // A UBL invoice should not include the AdditionalDocumentReference IssueTime


        // UBL-CR-114 warning
        // A UBL invoice should not include the AdditionalDocumentReference DocumentType


        // UBL-CR-115 warning
        // A UBL invoice should not include the AdditionalDocumentReference Xpath


        // UBL-CR-116 warning
        // A UBL invoice should not include the AdditionalDocumentReference LanguageID


        // UBL-CR-117 warning
        // A UBL invoice should not include the AdditionalDocumentReference LocaleCode


        // UBL-CR-118 warning
        // A UBL invoice should not include the AdditionalDocumentReference VersionID


        // UBL-CR-119 warning
        // A UBL invoice should not include the AdditionalDocumentReference DocumentStatusCode


        // UBL-CR-121 warning
        // A UBL invoice should not include the AdditionalDocumentReference Attachment External DocumentHash


        // UBL-CR-122 warning
        // A UBL invoice should not include the AdditionalDocumentReference Attachment External HashAlgorithmMethod


        // UBL-CR-123 warning
        // A UBL invoice should not include the AdditionalDocumentReference Attachment External ExpiryDate


        // UBL-CR-124 warning
        // A UBL invoice should not include the AdditionalDocumentReference Attachment External ExpiryTime


        // UBL-CR-125 warning
        // A UBL invoice should not include the AdditionalDocumentReference Attachment External MimeCode


        // UBL-CR-126 warning
        // A UBL invoice should not include the AdditionalDocumentReference Attachment External FormatCode


        // UBL-CR-127 warning
        // A UBL invoice should not include the AdditionalDocumentReference Attachment External EncodingCode


        // UBL-CR-128 warning
        // A UBL invoice should not include the AdditionalDocumentReference Attachment External CharacterSetCode


        // UBL-CR-129 warning
        // A UBL invoice should not include the AdditionalDocumentReference Attachment External FileName


        // UBL-CR-130 warning
        // A UBL invoice should not include the AdditionalDocumentReference Attachment External Descriprion


        // UBL-CR-131 warning
        // A UBL invoice should not include the AdditionalDocumentReference ValidityPeriod


        // UBL-CR-132 warning
        // A UBL invoice should not include the AdditionalDocumentReference IssuerParty


        // UBL-CR-133 warning
        // A UBL invoice should not include the AdditionalDocumentReference ResultOfVerification


        // UBL-CR-134 warning
        // A UBL invoice should not include the ProjectReference UUID


        // UBL-CR-135 warning
        // A UBL invoice should not include the ProjectReference IssueDate


        // UBL-CR-136 warning
        // A UBL invoice should not include the ProjectReference WorkPhaseReference


        // UBL-CR-137 warning
        // A UBL invoice should not include the Signature


        // UBL-CR-138 warning
        // A UBL invoice should not include the AccountingSupplierParty CustomerAssignedAccountID


        // UBL-CR-139 warning
        // A UBL invoice should not include the AccountingSupplierParty AdditionalAccountID


        // UBL-CR-140 warning
        // A UBL invoice should not include the AccountingSupplierParty DataSendingCapability


        // UBL-CR-141 warning
        // A UBL invoice should not include the AccountingSupplierParty Party MarkCareIndicator


        // UBL-CR-142 warning
        // A UBL invoice should not include the AccountingSupplierParty Party MarkAttentionIndicator


        // UBL-CR-143 warning
        // A UBL invoice should not include the AccountingSupplierParty Party WebsiteURI


        // UBL-CR-144 warning
        // A UBL invoice should not include the AccountingSupplierParty Party LogoReferenceID


        // UBL-CR-145 warning
        // A UBL invoice should not include the AccountingSupplierParty Party IndustryClassificationCode


        // UBL-CR-146 warning
        // A UBL invoice should not include the AccountingSupplierParty Party Language


        // UBL-CR-147 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress ID


        // UBL-CR-148 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress AddressTypeCode


        // UBL-CR-149 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress AddressFormatCode


        // UBL-CR-150 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress Postbox


        // UBL-CR-151 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress Floor


        // UBL-CR-152 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress Room


        // UBL-CR-153 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress BlockName


        // UBL-CR-154 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress BuildingName


        // UBL-CR-155 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress BuildingNumber


        // UBL-CR-156 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress InhouseMail


        // UBL-CR-157 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress Department


        // UBL-CR-158 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress MarkAttention


        // UBL-CR-159 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress MarkCare


        // UBL-CR-160 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress PlotIdentification


        // UBL-CR-161 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress CitySubdivisionName


        // UBL-CR-162 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress CountrySubentityCode


        // UBL-CR-163 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress Region


        // UBL-CR-164 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress District


        // UBL-CR-165 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress TimezoneOffset


        // UBL-CR-166 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress Country Name


        // UBL-CR-167 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PostalAddress LocationCoordinate


        // UBL-CR-168 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PhysicalLocation


        // UBL-CR-169 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyTaxScheme RegistrationName


        // UBL-CR-170 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyTaxScheme TaxLevelCode


        // UBL-CR-171 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyTaxScheme ExemptionReasonCode


        // UBL-CR-172 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyTaxScheme ExemptionReason


        // UBL-CR-173 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyTaxScheme RegistrationAddress


        // UBL-CR-174 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyTaxScheme TaxScheme Name


        // UBL-CR-175 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyTaxScheme TaxScheme TaxTypeCode


        // UBL-CR-176 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyTaxScheme TaxScheme CurrencyCode


        // UBL-CR-177 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyTaxScheme TaxScheme JurisdictionRegionAddress


        // UBL-CR-178 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyLegalEntity RegistrationDate


        // UBL-CR-179 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyLegalEntity RegistrationExpirationDate


        // UBL-CR-180 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyLegalEntity CompanyLegalFormCode


        // UBL-CR-181 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyLegalEntity SoleProprietorshipIndicator


        // UBL-CR-182 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyLegalEntity CompanyLiquidationStatusCode


        // UBL-CR-183 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyLegalEntity CorporateStockAmount


        // UBL-CR-184 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyLegalEntity FullyPaidSharesIndicator


        // UBL-CR-185 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyLegalEntity RegistrationAddress


        // UBL-CR-186 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyLegalEntity CorporateRegistrationScheme


        // UBL-CR-187 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyLegalEntity HeadOfficeParty


        // UBL-CR-188 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PartyLegalEntity ShareholderParty


        // UBL-CR-189 warning
        // A UBL invoice should not include the AccountingSupplierParty Party Contact ID


        // UBL-CR-190 warning
        // A UBL invoice should not include the AccountingSupplierParty Party Contact Telefax


        // UBL-CR-191 warning
        // A UBL invoice should not include the AccountingSupplierParty Party Contact Note


        // UBL-CR-192 warning
        // A UBL invoice should not include the AccountingSupplierParty Party Contact OtherCommunication


        // UBL-CR-193 warning
        // A UBL invoice should not include the AccountingSupplierParty Party Person


        // UBL-CR-194 warning
        // A UBL invoice should not include the AccountingSupplierParty Party AgentParty


        // UBL-CR-195 warning
        // A UBL invoice should not include the AccountingSupplierParty Party ServiceProviderParty


        // UBL-CR-196 warning
        // A UBL invoice should not include the AccountingSupplierParty Party PowerOfAttorney


        // UBL-CR-197 warning
        // A UBL invoice should not include the AccountingSupplierParty Party FinancialAccount


        // UBL-CR-198 warning
        // A UBL invoice should not include the AccountingSupplierParty DespatchContact


        // UBL-CR-199 warning
        // A UBL invoice should not include the AccountingSupplierParty AccountingContact


        // UBL-CR-200 warning
        // A UBL invoice should not include the AccountingSupplierParty SellerContact


        // UBL-CR-201 warning
        // A UBL invoice should not include the AccountingCustomerParty CustomerAssignedAccountID


        // UBL-CR-202 warning
        // A UBL invoice should not include the AccountingCustomerParty SupplierAssignedAccountID


        // UBL-CR-203 warning
        // A UBL invoice should not include the AccountingCustomerParty AdditionalAccountID


        // UBL-CR-204 warning
        // A UBL invoice should not include the AccountingCustomerParty Party MarkCareIndicator


        // UBL-CR-205 warning
        // A UBL invoice should not include the AccountingCustomerParty Party MarkAttentionIndicator


        // UBL-CR-206 warning
        // A UBL invoice should not include the AccountingCustomerParty Party WebsiteURI


        // UBL-CR-207 warning
        // A UBL invoice should not include the AccountingCustomerParty Party LogoReferenceID


        // UBL-CR-208 warning
        // A UBL invoice should not include the AccountingCustomerParty Party IndustryClassificationCode


        // UBL-CR-209 warning
        // A UBL invoice should not include the AccountingCustomerParty Party Language


        // UBL-CR-210 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress ID


        // UBL-CR-211 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress AddressTypeCode


        // UBL-CR-212 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress AddressFormatCode


        // UBL-CR-213 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress Postbox


        // UBL-CR-214 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress Floor


        // UBL-CR-215 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress Room


        // UBL-CR-216 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress BlockName


        // UBL-CR-217 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress BuildingName


        // UBL-CR-218 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress BuildingNumber


        // UBL-CR-219 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress InhouseMail


        // UBL-CR-220 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress Department


        // UBL-CR-221 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress MarkAttention


        // UBL-CR-222 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress MarkCare


        // UBL-CR-223 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress PlotIdentification


        // UBL-CR-224 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress CitySubdivisionName


        // UBL-CR-225 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress CountrySubentityCode


        // UBL-CR-226 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress Region


        // UBL-CR-227 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress District


        // UBL-CR-228 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress TimezoneOffset


        // UBL-CR-229 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress Country Name


        // UBL-CR-230 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PostalAddress LocationCoordinate


        // UBL-CR-231 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PhysicalLocation


        // UBL-CR-232 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyTaxScheme RegistrationName


        // UBL-CR-233 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyTaxScheme TaxLevelCode


        // UBL-CR-234 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyTaxScheme ExemptionReasonCode


        // UBL-CR-235 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyTaxScheme ExemptionReason


        // UBL-CR-236 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyTaxScheme RegistrationAddress


        // UBL-CR-237 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyTaxScheme TaxScheme Name


        // UBL-CR-238 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyTaxScheme TaxScheme TaxTypeCode


        // UBL-CR-239 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyTaxScheme TaxScheme CurrencyCode


        // UBL-CR-240 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyTaxScheme TaxScheme JurisdictionRegionAddress


        // UBL-CR-241 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyLegalEntity RegistrationDate


        // UBL-CR-242 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyLegalEntity RegistrationExpirationDate


        // UBL-CR-243 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyLegalEntity CompanyLegalFormCode


        // UBL-CR-244 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyLegalEntity CompanyLegalForm


        // UBL-CR-245 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyLegalEntity SoleProprietorshipIndicator


        // UBL-CR-246 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyLegalEntity CompanyLiquidationStatusCode


        // UBL-CR-247 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyLegalEntity CorporateStockAmount


        // UBL-CR-248 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyLegalEntity FullyPaidSharesIndicator


        // UBL-CR-249 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyLegalEntity RegistrationAddress


        // UBL-CR-250 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyLegalEntity CorporateRegistrationScheme


        // UBL-CR-251 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyLegalEntity HeadOfficeParty


        // UBL-CR-252 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PartyLegalEntity ShareholderParty


        // UBL-CR-253 warning
        // A UBL invoice should not include the AccountingCustomerParty Party Contact ID


        // UBL-CR-254 warning
        // A UBL invoice should not include the AccountingCustomerParty Party Contact Telefax


        // UBL-CR-255 warning
        // A UBL invoice should not include the AccountingCustomerParty Party Contact Note


        // UBL-CR-256 warning
        // A UBL invoice should not include the AccountingCustomerParty Party Contact OtherCommunication


        // UBL-CR-257 warning
        // A UBL invoice should not include the AccountingCustomerParty Party Person


        // UBL-CR-258 warning
        // A UBL invoice should not include the AccountingCustomerParty Party AgentParty


        // UBL-CR-259 warning
        // A UBL invoice should not include the AccountingCustomerParty Party ServiceProviderParty


        // UBL-CR-260 warning
        // A UBL invoice should not include the AccountingCustomerParty Party PowerOfAttorney


        // UBL-CR-261 warning
        // A UBL invoice should not include the AccountingCustomerParty Party FinancialAccount


        // UBL-CR-262 warning
        // A UBL invoice should not include the AccountingCustomerParty DeliveryContact


        // UBL-CR-263 warning
        // A UBL invoice should not include the AccountingCustomerParty AccountingContact


        // UBL-CR-264 warning
        // A UBL invoice should not include the AccountingCustomerParty BuyerContact


        // UBL-CR-265 warning
        // A UBL invoice should not include the PayeeParty MarkCareIndicator


        // UBL-CR-266 warning
        // A UBL invoice should not include the PayeeParty MarkAttentionIndicator


        // UBL-CR-267 warning
        // A UBL invoice should not include the PayeeParty WebsiteURI


        // UBL-CR-268 warning
        // A UBL invoice should not include the PayeeParty LogoReferenceID


        // UBL-CR-269 warning
        // A UBL invoice should not include the PayeeParty EndpointID


        // UBL-CR-270 warning
        // A UBL invoice should not include the PayeeParty IndustryClassificationCode


        // UBL-CR-271 warning
        // A UBL invoice should not include the PayeeParty Language


        // UBL-CR-272 warning
        // A UBL invoice should not include the PayeeParty PostalAddress


        // UBL-CR-273 warning
        // A UBL invoice should not include the PayeeParty PhysicalLocation


        // UBL-CR-274 warning
        // A UBL invoice should not include the PayeeParty PartyTaxScheme


        // UBL-CR-275 warning
        // A UBL invoice should not include the PayeeParty PartyLegalEntity RegistrationName


        // UBL-CR-276 warning
        // A UBL invoice should not include the PayeeParty PartyLegalEntity RegistrationDate


        // UBL-CR-277 warning
        // A UBL invoice should not include the PayeeParty PartyLegalEntity RegistrationExpirationDate


        // UBL-CR-278 warning
        // A UBL invoice should not include the PayeeParty PartyLegalEntity CompanyLegalFormCode


        // UBL-CR-279 warning
        // A UBL invoice should not include the PayeeParty PartyLegalEntity CompanyLegalForm


        // UBL-CR-280 warning
        // A UBL invoice should not include the PayeeParty PartyLegalEntity SoleProprietorshipIndicator


        // UBL-CR-281 warning
        // A UBL invoice should not include the PayeeParty PartyLegalEntity CompanyLiquidationStatusCode


        // UBL-CR-282 warning
        // A UBL invoice should not include the PayeeParty PartyLegalEntity CorporateStockAmount


        // UBL-CR-283 warning
        // A UBL invoice should not include the PayeeParty PartyLegalEntity FullyPaidSharesIndicator


        // UBL-CR-284 warning
        // A UBL invoice should not include the PayeeParty PartyLegalEntity RegistrationAddress


        // UBL-CR-285 warning
        // A UBL invoice should not include the PayeeParty PartyLegalEntity CorporateRegistrationScheme


        // UBL-CR-286 warning
        // A UBL invoice should not include the PayeeParty PartyLegalEntity HeadOfficeParty


        // UBL-CR-287 warning
        // A UBL invoice should not include the PayeeParty PartyLegalEntity ShareholderParty


        // UBL-CR-288 warning
        // A UBL invoice should not include the PayeeParty Contact


        // UBL-CR-289 warning
        // A UBL invoice should not include the PayeeParty Person


        // UBL-CR-290 warning
        // A UBL invoice should not include the PayeeParty AgentParty


        // UBL-CR-291 warning
        // A UBL invoice should not include the PayeeParty ServiceProviderParty


        // UBL-CR-292 warning
        // A UBL invoice should not include the PayeeParty PowerOfAttorney


        // UBL-CR-293 warning
        // A UBL invoice should not include the PayeeParty FinancialAccount


        // UBL-CR-294 warning
        // A UBL invoice should not include the BuyerCustomerParty


        // UBL-CR-295 warning
        // A UBL invoice should not include the SellerSupplierParty


        // UBL-CR-296 warning
        // A UBL invoice should not include the TaxRepresentativeParty MarkCareIndicator


        // UBL-CR-297 warning
        // A UBL invoice should not include the TaxRepresentativeParty MarkAttentionIndicator


        // UBL-CR-298 warning
        // A UBL invoice should not include the TaxRepresentativeParty WebsiteURI


        // UBL-CR-299 warning
        // A UBL invoice should not include the TaxRepresentativeParty LogoReferenceID


        // UBL-CR-300 warning
        // A UBL invoice should not include the TaxRepresentativeParty EndpointID


        // UBL-CR-301 warning
        // A UBL invoice should not include the TaxRepresentativeParty IndustryClassificationCode


        // UBL-CR-302 warning
        // A UBL invoice should not include the TaxRepresentativeParty PartyIdentification


        // UBL-CR-303 warning
        // A UBL invoice should not include the TaxRepresentativeParty Language


        // UBL-CR-304 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress ID


        // UBL-CR-305 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress AddressTypeCode


        // UBL-CR-306 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress AddressFormatCode


        // UBL-CR-307 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress Postbox


        // UBL-CR-308 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress Floor


        // UBL-CR-309 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress Room


        // UBL-CR-310 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress BlockName


        // UBL-CR-311 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress BuildingName


        // UBL-CR-312 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress BuildingNumber


        // UBL-CR-313 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress InhouseMail


        // UBL-CR-314 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress Department


        // UBL-CR-315 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress MarkAttention


        // UBL-CR-316 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress MarkCare


        // UBL-CR-317 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress PlotIdentification


        // UBL-CR-318 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress CitySubdivisionName


        // UBL-CR-319 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress CountrySubentityCode


        // UBL-CR-320 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress Region


        // UBL-CR-321 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress District


        // UBL-CR-322 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress TimezoneOffset


        // UBL-CR-323 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress Country Name


        // UBL-CR-324 warning
        // A UBL invoice should not include the TaxRepresentativeParty PostalAddress LocationCoordinate


        // UBL-CR-325 warning
        // A UBL invoice should not include the TaxRepresentativeParty PhysicalLocation


        // UBL-CR-326 warning
        // A UBL invoice should not include the TaxRepresentativeParty PartyTaxScheme RegistrationName


        // UBL-CR-327 warning
        // A UBL invoice should not include the TaxRepresentativeParty PartyTaxScheme TaxLevelCode


        // UBL-CR-328 warning
        // A UBL invoice should not include the TaxRepresentativeParty PartyTaxScheme ExemptionReasonCode


        // UBL-CR-329 warning
        // A UBL invoice should not include the TaxRepresentativeParty PartyTaxScheme ExemptionReason


        // UBL-CR-330 warning
        // A UBL invoice should not include the TaxRepresentativeParty PartyTaxScheme RegistrationAddress


        // UBL-CR-331 warning
        // A UBL invoice should not include the TaxRepresentativeParty PartyTaxScheme TaxScheme Name


        // UBL-CR-332 warning
        // A UBL invoice should not include the TaxRepresentativeParty PartyTaxScheme TaxScheme TaxTypeCode


        // UBL-CR-333 warning
        // A UBL invoice should not include the TaxRepresentativeParty PartyTaxScheme TaxScheme CurrencyCode


        // UBL-CR-334 warning
        // A UBL invoice should not include the TaxRepresentativeParty PartyTaxScheme TaxScheme JurisdictionRegionAddress


        // UBL-CR-335 warning
        // A UBL invoice should not include the TaxRepresentativeParty PartyLegalEntity


        // UBL-CR-336 warning
        // A UBL invoice should not include the TaxRepresentativeParty Contact


        // UBL-CR-337 warning
        // A UBL invoice should not include the TaxRepresentativeParty Person


        // UBL-CR-338 warning
        // A UBL invoice should not include the TaxRepresentativeParty AgentParty


        // UBL-CR-339 warning
        // A UBL invoice should not include the TaxRepresentativeParty ServiceProviderParty


        // UBL-CR-340 warning
        // A UBL invoice should not include the TaxRepresentativeParty PowerOfAttorney


        // UBL-CR-341 warning
        // A UBL invoice should not include the TaxRepresentativeParty FinancialAccount


        // UBL-CR-342 warning
        // A UBL invoice should not include the Delivery ID


        // UBL-CR-343 warning
        // A UBL invoice should not include the Delivery Quantity


        // UBL-CR-344 warning
        // A UBL invoice should not include the Delivery MinimumQuantity


        // UBL-CR-345 warning
        // A UBL invoice should not include the Delivery MaximumQuantity


        // UBL-CR-346 warning
        // A UBL invoice should not include the Delivery ActualDeliveryTime


        // UBL-CR-347 warning
        // A UBL invoice should not include the Delivery LatestDeliveryDate


        // UBL-CR-348 warning
        // A UBL invoice should not include the Delivery LatestDeliveryTime


        // UBL-CR-349 warning
        // A UBL invoice should not include the Delivery ReleaseID


        // UBL-CR-350 warning
        // A UBL invoice should not include the Delivery TrackingID


        // UBL-CR-351 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Description


        // UBL-CR-352 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Conditions


        // UBL-CR-353 warning
        // A UBL invoice should not include the Delivery DeliveryLocation CountrySubentity


        // UBL-CR-354 warning
        // A UBL invoice should not include the Delivery DeliveryLocation CountrySubentityCode


        // UBL-CR-355 warning
        // A UBL invoice should not include the Delivery DeliveryLocation LocationTypeCode


        // UBL-CR-356 warning
        // A UBL invoice should not include the Delivery DeliveryLocation InformationURI


        // UBL-CR-357 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Name


        // UBL-CR-358 warning
        // A UBL invoice should not include the Delivery DeliveryLocation ValidityPeriod


        // UBL-CR-359 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address ID


        // UBL-CR-360 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address AddressTypeCode


        // UBL-CR-361 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address AddressFormatCode


        // UBL-CR-362 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address Postbox


        // UBL-CR-363 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address Floor


        // UBL-CR-364 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address Room


        // UBL-CR-365 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address BlockName


        // UBL-CR-366 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address BuildingName


        // UBL-CR-367 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address BuildingNumber


        // UBL-CR-368 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address InhouseMail


        // UBL-CR-369 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address Department


        // UBL-CR-370 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address MarkAttention


        // UBL-CR-371 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address MarkCare


        // UBL-CR-372 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address PlotIdentification


        // UBL-CR-373 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address CitySubdivisionName


        // UBL-CR-374 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address CountrySubentityCode


        // UBL-CR-375 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address Region


        // UBL-CR-376 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address District


        // UBL-CR-377 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address TimezoneOffset


        // UBL-CR-378 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address Country Name


        // UBL-CR-379 warning
        // A UBL invoice should not include the Delivery DeliveryLocation Address LocationCoordinate


        // UBL-CR-380 warning
        // A UBL invoice should not include the Delivery DeliveryLocation SubsidiaryLocation


        // UBL-CR-381 warning
        // A UBL invoice should not include the Delivery DeliveryLocation LocationCoordinate


        // UBL-CR-382 warning
        // A UBL invoice should not include the Delivery AlternativeDeliveryLocation


        // UBL-CR-383 warning
        // A UBL invoice should not include the Delivery RequestedDeliveryPeriod


        // UBL-CR-384 warning
        // A UBL invoice should not include the Delivery EstimatedDeliveryPeriod


        // UBL-CR-385 warning
        // A UBL invoice should not include the Delivery CarrierParty


        // UBL-CR-386 warning
        // A UBL invoice should not include the DeliveryParty MarkCareIndicator


        // UBL-CR-387 warning
        // A UBL invoice should not include the DeliveryParty MarkAttentionIndicator


        // UBL-CR-388 warning
        // A UBL invoice should not include the DeliveryParty WebsiteURI


        // UBL-CR-389 warning
        // A UBL invoice should not include the DeliveryParty LogoReferenceID


        // UBL-CR-390 warning
        // A UBL invoice should not include the DeliveryParty EndpointID


        // UBL-CR-391 warning
        // A UBL invoice should not include the DeliveryParty IndustryClassificationCode


        // UBL-CR-392 warning
        // A UBL invoice should not include the DeliveryParty PartyIdentification


        // UBL-CR-393 warning
        // A UBL invoice should not include the DeliveryParty Language


        // UBL-CR-394 warning
        // A UBL invoice should not include the DeliveryParty PostalAddress


        // UBL-CR-395 warning
        // A UBL invoice should not include the DeliveryParty PhysicalLocation


        // UBL-CR-396 warning
        // A UBL invoice should not include the DeliveryParty PartyTaxScheme


        // UBL-CR-397 warning
        // A UBL invoice should not include the DeliveryParty PartyLegalEntity


        // UBL-CR-398 warning
        // A UBL invoice should not include the DeliveryParty Contact


        // UBL-CR-399 warning
        // A UBL invoice should not include the DeliveryParty Person


        // UBL-CR-400 warning
        // A UBL invoice should not include the DeliveryParty AgentParty


        // UBL-CR-401 warning
        // A UBL invoice should not include the DeliveryParty ServiceProviderParty


        // UBL-CR-402 warning
        // A UBL invoice should not include the DeliveryParty PowerOfAttorney


        // UBL-CR-403 warning
        // A UBL invoice should not include the DeliveryParty FinancialAccount


        // UBL-CR-404 warning
        // A UBL invoice should not include the Delivery NotifyParty


        // UBL-CR-405 warning
        // A UBL invoice should not include the Delivery Despatch


        // UBL-CR-406 warning
        // A UBL invoice should not include the Delivery DeliveryTerms


        // UBL-CR-407 warning
        // A UBL invoice should not include the Delivery MinimumDeliveryUnit


        // UBL-CR-408 warning
        // A UBL invoice should not include the Delivery MaximumDeliveryUnit


        // UBL-CR-409 warning
        // A UBL invoice should not include the Delivery Shipment


        // UBL-CR-410 warning
        // A UBL invoice should not include the DeliveryTerms


        // UBL-CR-411 warning
        // A UBL invoice should not include the PaymentMeans ID


        // UBL-CR-412 warning
        // A UBL invoice should not include the PaymentMeans PaymentDueDate


        // UBL-CR-413 warning
        // A UBL invoice should not include the PaymentMeans PaymentChannelCode


        // UBL-CR-414 warning
        // A UBL invoice should not include the PaymentMeans InstructionID


        // UBL-CR-415 warning
        // A UBL invoice should not include the PaymentMeans CardAccount CardTypeCode


        // UBL-CR-416 warning
        // A UBL invoice should not include the PaymentMeans CardAccount ValidityStartDate


        // UBL-CR-417 warning
        // A UBL invoice should not include the PaymentMeans CardAccount ExpiryDate


        // UBL-CR-418 warning
        // A UBL invoice should not include the PaymentMeans CardAccount IssuerID


        // UBL-CR-419 warning
        // A UBL invoice should not include the PaymentMeans CardAccount IssueNumberID


        // UBL-CR-420 warning
        // A UBL invoice should not include the PaymentMeans CardAccount CV2ID


        // UBL-CR-421 warning
        // A UBL invoice should not include the PaymentMeans CardAccount CardChipCode


        // UBL-CR-422 warning
        // A UBL invoice should not include the PaymentMeans CardAccount ChipApplicationID


        // UBL-CR-424 warning
        // A UBL invoice should not include the PaymentMeans PayeeFinancialAccount AliasName


        // UBL-CR-425 warning
        // A UBL invoice should not include the PaymentMeans PayeeFinancialAccount AccountTypeCode


        // UBL-CR-426 warning
        // A UBL invoice should not include the PaymentMeans PayeeFinancialAccount AccountFormatCode


        // UBL-CR-427 warning
        // A UBL invoice should not include the PaymentMeans PayeeFinancialAccount CurrencyCode


        // UBL-CR-428 warning
        // A UBL invoice should not include the PaymentMeans PayeeFinancialAccount PaymentNote


        // UBL-CR-429 warning
        // A UBL invoice should not include the PaymentMeans PayeeFinancialAccount FinancialInstitutionBranch Name


        // UBL-CR-430 warning
        // A UBL invoice should not include the PaymentMeans PayeeFinancialAccount FinancialInstitutionBranch FinancialInstitution Name


        // UBL-CR-431 warning
        // A UBL invoice should not include the PaymentMeans PayeeFinancialAccount FinancialInstitutionBranch FinancialInstitution Address


        // UBL-CR-432 warning
        // A UBL invoice should not include the PaymentMeans PayeeFinancialAccount FinancialInstitutionBranch Address


        // UBL-CR-433 warning
        // A UBL invoice should not include the PaymentMeans PayeeFinancialAccount Country


        // UBL-CR-434 warning
        // A UBL invoice should not include the PaymentMeans CreditAccount


        // UBL-CR-435 warning
        // A UBL invoice should not include the PaymentMeans PaymentMandate MandateTypeCode


        // UBL-CR-436 warning
        // A UBL invoice should not include the PaymentMeans PaymentMandate MaximumPaymentInstructionsNumeric


        // UBL-CR-437 warning
        // A UBL invoice should not include the PaymentMeans PaymentMandate MaximumPaidAmount


        // UBL-CR-438 warning
        // A UBL invoice should not include the PaymentMeans PaymentMandate SignatureID


        // UBL-CR-439 warning
        // A UBL invoice should not include the PaymentMeans PaymentMandate PayerParty


        // UBL-CR-440 warning
        // A UBL invoice should not include the PaymentMeans PaymentMandate PayerFinancialAccount Name


        // UBL-CR-441 warning
        // A UBL invoice should not include the PaymentMeans PaymentMandate PayerFinancialAccount AliasName


        // UBL-CR-442 warning
        // A UBL invoice should not include the PaymentMeans PaymentMandate PayerFinancialAccount AccountTypeCode


        // UBL-CR-443 warning
        // A UBL invoice should not include the PaymentMeans PaymentMandate PayerFinancialAccount AccountFormatCode


        // UBL-CR-444 warning
        // A UBL invoice should not include the PaymentMeans PaymentMandate PayerFinancialAccount CurrencyCode


        // UBL-CR-445 warning
        // A UBL invoice should not include the PaymentMeans PaymentMandate PayerFinancialAccount PaymentNote


        // UBL-CR-446 warning
        // A UBL invoice should not include the PaymentMeans PaymentMandate PayerFinancialAccount FinancialInstitutionBranch


        // UBL-CR-447 warning
        // A UBL invoice should not include the PaymentMeans PaymentMandate PayerFinancialAccount Country


        // UBL-CR-448 warning
        // A UBL invoice should not include the PaymentMeans PaymentMandate ValidityPeriod


        // UBL-CR-449 warning
        // A UBL invoice should not include the PaymentMeans PaymentMandate PaymentReversalPeriod


        // UBL-CR-450 warning
        // A UBL invoice should not include the PaymentMeans PaymentMandate Clause


        // UBL-CR-451 warning
        // A UBL invoice should not include the PaymentMeans TradeFinancing


        // UBL-CR-452 warning
        // A UBL invoice should not include the PaymentTerms ID


        // UBL-CR-453 warning
        // A UBL invoice should not include the PaymentTerms PaymentMeansID


        // UBL-CR-454 warning
        // A UBL invoice should not include the PaymentTerms PrepaidPaymentReferenceID


        // UBL-CR-455 warning
        // A UBL invoice should not include the PaymentTerms ReferenceEventCode


        // UBL-CR-456 warning
        // A UBL invoice should not include the PaymentTerms SettlementDiscountPercent


        // UBL-CR-457 warning
        // A UBL invoice should not include the PaymentTerms PenaltySurchargePercent


        // UBL-CR-458 warning
        // A UBL invoice should not include the PaymentTerms PaymentPercent


        // UBL-CR-459 warning
        // A UBL invoice should not include the PaymentTerms Amount


        // UBL-CR-460 warning
        // A UBL invoice should not include the PaymentTerms SettlementDiscountAmount


        // UBL-CR-461 warning
        // A UBL invoice should not include the PaymentTerms PenaltyAmount


        // UBL-CR-462 warning
        // A UBL invoice should not include the PaymentTerms PaymentTermsDetailsURI


        // UBL-CR-463 warning
        // A UBL invoice should not include the PaymentTerms PaymentDueDate


        // UBL-CR-464 warning
        // A UBL invoice should not include the PaymentTerms InstallmentDueDate


        // UBL-CR-465 warning
        // A UBL invoice should not include the PaymentTerms InvoicingPartyReference


        // UBL-CR-466 warning
        // A UBL invoice should not include the PaymentTerms SettlementPeriod


        // UBL-CR-467 warning
        // A UBL invoice should not include the PaymentTerms PenaltyPeriod


        // UBL-CR-468 warning
        // A UBL invoice should not include the PaymentTerms ExchangeRate


        // UBL-CR-469 warning
        // A UBL invoice should not include the PaymentTerms ValidityPeriod


        // UBL-CR-470 warning
        // A UBL invoice should not include the PrepaidPayment


        // UBL-CR-471 warning
        // A UBL invoice should not include the AllowanceCharge ID


        // UBL-CR-472 warning
        // A UBL invoice should not include the AllowanceCharge PrepaidIndicator


        // UBL-CR-473 warning
        // A UBL invoice should not include the AllowanceCharge SequenceNumeric


        // UBL-CR-474 warning
        // A UBL invoice should not include the AllowanceCharge AccountingCostCode


        // UBL-CR-475 warning
        // A UBL invoice should not include the AllowanceCharge AccountingCost


        // UBL-CR-476 warning
        // A UBL invoice should not include the AllowanceCharge PerUnitAmount


        // UBL-CR-477 warning
        // A UBL invoice should not include the AllowanceCharge TaxCategory Name


        // UBL-CR-478 warning
        // A UBL invoice should not include the AllowanceCharge TaxCategory BaseUnitMeasure


        // UBL-CR-479 warning
        // A UBL invoice should not include the AllowanceCharge TaxCategory PerUnitAmount


        // UBL-CR-480 warning
        // A UBL invoice should not include the AllowanceCharge TaxCategory TaxExemptionReasonCode


        // UBL-CR-481 warning
        // A UBL invoice should not include the AllowanceCharge TaxCategory TaxExemptionReason


        // UBL-CR-482 warning
        // A UBL invoice should not include the AllowanceCharge TaxCategory TierRange


        // UBL-CR-483 warning
        // A UBL invoice should not include the AllowanceCharge TaxCategory TierRatePercent


        // UBL-CR-484 warning
        // A UBL invoice should not include the AllowanceCharge TaxCategory TaxScheme Name


        // UBL-CR-485 warning
        // A UBL invoice should not include the AllowanceCharge TaxCategory TaxScheme TaxTypeCode


        // UBL-CR-486 warning
        // A UBL invoice should not include the AllowanceCharge TaxCategory TaxScheme CurrencyCode


        // UBL-CR-487 warning
        // A UBL invoice should not include the AllowanceCharge TaxCategory TaxScheme JurisdictionRegionAddress


        // UBL-CR-488 warning
        // A UBL invoice should not include the AllowanceCharge TaxTotal


        // UBL-CR-489 warning
        // A UBL invoice should not include the AllowanceCharge PaymentMeans


        // UBL-CR-490 warning
        // A UBL invoice should not include the TaxExchangeRate


        // UBL-CR-491 warning
        // A UBL invoice should not include the PricingExchangeRate


        // UBL-CR-492 warning
        // A UBL invoice should not include the PaymentExchangeRate


        // UBL-CR-493 warning
        // A UBL invoice should not include the PaymentAlternativeExchangeRate


        // UBL-CR-494 warning
        // A UBL invoice should not include the TaxTotal RoundingAmount


        // UBL-CR-495 warning
        // A UBL invoice should not include the TaxTotal TaxEvidenceIndicator


        // UBL-CR-496 warning
        // A UBL invoice should not include the TaxTotal TaxIncludedIndicator


        // UBL-CR-497 warning
        // A UBL invoice should not include the TaxTotal TaxSubtotal CalulationSequenceNumeric


        // UBL-CR-498 warning
        // A UBL invoice should not include the TaxTotal TaxSubtotal TransactionCurrencyTaxAmount


        // UBL-CR-499 warning
        // A UBL invoice should not include the TaxTotal TaxSubtotal Percent


        // UBL-CR-500 warning
        // A UBL invoice should not include the TaxTotal TaxSubtotal BaseUnitMeasure


        // UBL-CR-501 warning
        // A UBL invoice should not include the TaxTotal TaxSubtotal PerUnitAmount


        // UBL-CR-502 warning
        // A UBL invoice should not include the TaxTotal TaxSubtotal TierRange


        // UBL-CR-503 warning
        // A UBL invoice should not include the TaxTotal TaxSubtotal TierRatePercent


        // UBL-CR-504 warning
        // A UBL invoice should not include the TaxTotal TaxSubtotal TaxCategory Name


        // UBL-CR-505 warning
        // A UBL invoice should not include the TaxTotal TaxSubtotal TaxCategory BaseUnitMeasure


        // UBL-CR-506 warning
        // A UBL invoice should not include the TaxTotal TaxSubtotal TaxCategory PerUnitAmount


        // UBL-CR-507 warning
        // A UBL invoice should not include the TaxTotal TaxSubtotal TaxCategory TierRange


        // UBL-CR-508 warning
        // A UBL invoice should not include the TaxTotal TaxSubtotal TaxCategory TierRatePercent


        // UBL-CR-509 warning
        // A UBL invoice should not include the TaxTotal TaxSubtotal TaxCategory TaxScheme Name


        // UBL-CR-510 warning
        // A UBL invoice should not include the TaxTotal TaxSubtotal TaxCategory TaxScheme TaxTypeCode


        // UBL-CR-511 warning
        // A UBL invoice should not include the TaxTotal TaxSubtotal TaxCategory TaxScheme CurrencyCode


        // UBL-CR-512 warning
        // A UBL invoice should not include the TaxTotal TaxSubtotal TaxCategory TaxScheme JurisdictionRegionAddress


        // UBL-CR-513 warning
        // A UBL invoice should not include the WithholdingTaxTotal


        // UBL-CR-514 warning
        // A UBL invoice should not include the LegalMonetaryTotal PayableAlternativeAmount


        // UBL-CR-515 warning
        // A UBL invoice should not include the InvoiceLine UUID


        // UBL-CR-516 warning
        // A UBL invoice should not include the InvoiceLine TaxPointDate


        // UBL-CR-517 warning
        // A UBL invoice should not include the InvoiceLine AccountingCostCode


        // UBL-CR-518 warning
        // A UBL invoice should not include the InvoiceLine PaymentPurposeCode


        // UBL-CR-519 warning
        // A UBL invoice should not include the InvoiceLine FreeOfChargeIndicator


        // UBL-CR-520 warning
        // A UBL invoice should not include the InvoiceLine InvoicePeriod StartTime


        // UBL-CR-521 warning
        // A UBL invoice should not include the InvoiceLine InvoicePeriod EndTime


        // UBL-CR-522 warning
        // A UBL invoice should not include the InvoiceLine InvoicePeriod DurationMeasure


        // UBL-CR-523 warning
        // A UBL invoice should not include the InvoiceLine InvoicePeriod DescriptionCode


        // UBL-CR-524 warning
        // A UBL invoice should not include the InvoiceLine InvoicePeriod Description


        // UBL-CR-525 warning
        // A UBL invoice should not include the InvoiceLine OrderLineReference SalesOrderLineID


        // UBL-CR-526 warning
        // A UBL invoice should not include the InvoiceLine OrderLineReference UUID


        // UBL-CR-527 warning
        // A UBL invoice should not include the InvoiceLine OrderLineReference LineStatusCode


        // UBL-CR-528 warning
        // A UBL invoice should not include the InvoiceLine OrderLineReference OrderReference


        // UBL-CR-529 warning
        // A UBL invoice should not include the InvoiceLine DespatchLineReference


        // UBL-CR-530 warning
        // A UBL invoice should not include the InvoiceLine ReceiptLineReference


        // UBL-CR-531 warning
        // A UBL invoice should not include the InvoiceLine BillingReference


        // UBL-CR-532 warning
        // A UBL invoice should not include the InvoiceLine DocumentReference CopyIndicator


        // UBL-CR-533 warning
        // A UBL invoice should not include the InvoiceLine DocumentReference UUID


        // UBL-CR-534 warning
        // A UBL invoice should not include the InvoiceLine DocumentReference IssueDate


        // UBL-CR-535 warning
        // A UBL invoice should not include the InvoiceLine DocumentReference IssueTime


        // UBL-CR-537 warning
        // A UBL invoice should not include the InvoiceLine DocumentReference DocumentType


        // UBL-CR-538 warning
        // A UBL invoice should not include the InvoiceLine DocumentReference Xpath


        // UBL-CR-539 warning
        // A UBL invoice should not include the InvoiceLine DocumentReference LanguageID


        // UBL-CR-540 warning
        // A UBL invoice should not include the InvoiceLine DocumentReference LocaleCode


        // UBL-CR-541 warning
        // A UBL invoice should not include the InvoiceLine DocumentReference VersionID


        // UBL-CR-542 warning
        // A UBL invoice should not include the InvoiceLine DocumentReference DocumentStatusCode


        // UBL-CR-543 warning
        // A UBL invoice should not include the InvoiceLine DocumentReference DocumentDescription


        // UBL-CR-544 warning
        // A UBL invoice should not include the InvoiceLine DocumentReference Attachment


        // UBL-CR-545 warning
        // A UBL invoice should not include the InvoiceLine DocumentReference ValidityPeriod


        // UBL-CR-546 warning
        // A UBL invoice should not include the InvoiceLine DocumentReference IssuerParty


        // UBL-CR-547 warning
        // A UBL invoice should not include the InvoiceLine DocumentReference ResultOfVerification


        // UBL-CR-548 warning
        // A UBL invoice should not include the InvoiceLine PricingReference


        // UBL-CR-549 warning
        // A UBL invoice should not include the InvoiceLine OriginatorParty


        // UBL-CR-550 warning
        // A UBL invoice should not include the InvoiceLine Delivery


        // UBL-CR-551 warning
        // A UBL invoice should not include the InvoiceLine PaymentTerms


        // UBL-CR-552 warning
        // A UBL invoice should not include the InvoiceLine AllowanceCharge ID


        // UBL-CR-553 warning
        // A UBL invoice should not include the InvoiceLine AllowanceCharge PrepaidIndicator


        // UBL-CR-554 warning
        // A UBL invoice should not include the InvoiceLine AllowanceCharge SequenceNumeric


        // UBL-CR-555 warning
        // A UBL invoice should not include the InvoiceLine AllowanceCharge AccountingCostCode


        // UBL-CR-556 warning
        // A UBL invoice should not include the InvoiceLine AllowanceCharge AccountingCost


        // UBL-CR-557 warning
        // A UBL invoice should not include the InvoiceLine AllowanceCharge PerUnitAmount


        // UBL-CR-558 warning
        // A UBL invoice should not include the InvoiceLine AllowanceCharge TaxCategory


        // UBL-CR-559 warning
        // A UBL invoice should not include the InvoiceLine AllowanceCharge TaxTotal


        // UBL-CR-560 warning
        // A UBL invoice should not include the InvoiceLine AllowanceCharge PaymentMeans


        // UBL-CR-561 warning
        // A UBL invoice should not include the InvoiceLine TaxTotal


        // UBL-CR-562 warning
        // A UBL invoice should not include the InvoiceLine WithholdingTaxTotal


        // UBL-CR-563 warning
        // A UBL invoice should not include the InvoiceLine Item PackQuantity


        // UBL-CR-564 warning
        // A UBL invoice should not include the InvoiceLine Item PackSizeNumeric


        // UBL-CR-565 warning
        // A UBL invoice should not include the InvoiceLine Item CatalogueIndicator


        // UBL-CR-566 warning
        // A UBL invoice should not include the InvoiceLine Item HazardousRiskIndicator


        // UBL-CR-567 warning
        // A UBL invoice should not include the InvoiceLine Item AdditionalInformation


        // UBL-CR-568 warning
        // A UBL invoice should not include the InvoiceLine Item Keyword


        // UBL-CR-569 warning
        // A UBL invoice should not include the InvoiceLine Item BrandName


        // UBL-CR-570 warning
        // A UBL invoice should not include the InvoiceLine Item ModelName


        // UBL-CR-571 warning
        // A UBL invoice should not include the InvoiceLine Item BuyersItemIdentification ExtendedID


        // UBL-CR-572 warning
        // A UBL invoice should not include the InvoiceLine Item BuyersItemIdentification BarcodeSymbologyID


        // UBL-CR-573 warning
        // A UBL invoice should not include the InvoiceLine Item BuyersItemIdentification PhysicalAttribute


        // UBL-CR-574 warning
        // A UBL invoice should not include the InvoiceLine Item BuyersItemIdentification MeasurementDimension


        // UBL-CR-575 warning
        // A UBL invoice should not include the InvoiceLine Item BuyersItemIdentification IssuerParty


        // UBL-CR-576 warning
        // A UBL invoice should not include the InvoiceLine Item SellersItemIdentification ExtendedID


        // UBL-CR-577 warning
        // A UBL invoice should not include the InvoiceLine Item SellersItemIdentification BarcodeSymbologyID


        // UBL-CR-578 warning
        // A UBL invoice should not include the InvoiceLine Item SellersItemIdentification PhysicalAttribute


        // UBL-CR-579 warning
        // A UBL invoice should not include the InvoiceLine Item SellersItemIdentification MeasurementDimension


        // UBL-CR-580 warning
        // A UBL invoice should not include the InvoiceLine Item SellersItemIdentification IssuerParty


        // UBL-CR-581 warning
        // A UBL invoice should not include the InvoiceLine Item ManufacturersItemIdentification


        // UBL-CR-582 warning
        // A UBL invoice should not include the InvoiceLine Item StandardItemIdentification ExtendedID


        // UBL-CR-583 warning
        // A UBL invoice should not include the InvoiceLine Item StandardItemIdentification BarcodeSymbologyID


        // UBL-CR-584 warning
        // A UBL invoice should not include the InvoiceLine Item StandardItemIdentification PhysicalAttribute


        // UBL-CR-585 warning
        // A UBL invoice should not include the InvoiceLine Item StandardItemIdentification MeasurementDimension


        // UBL-CR-586 warning
        // A UBL invoice should not include the InvoiceLine Item StandardItemIdentification IssuerParty


        // UBL-CR-587 warning
        // A UBL invoice should not include the InvoiceLine Item CatalogueItemIdentification


        // UBL-CR-588 warning
        // A UBL invoice should not include the InvoiceLine Item AdditionalItemIdentification


        // UBL-CR-589 warning
        // A UBL invoice should not include the InvoiceLine Item CatalogueDocumentReference


        // UBL-CR-590 warning
        // A UBL invoice should not include the InvoiceLine Item ItemSpecificationDocumentReference


        // UBL-CR-591 warning
        // A UBL invoice should not include the InvoiceLine Item OriginCountry Name


        // UBL-CR-592 warning
        // A UBL invoice should not include the InvoiceLine Item CommodityClassification NatureCode


        // UBL-CR-593 warning
        // A UBL invoice should not include the InvoiceLine Item CommodityClassification CargoTypeCode


        // UBL-CR-594 warning
        // A UBL invoice should not include the InvoiceLine Item CommodityClassification CommodityCode


        // UBL-CR-595 warning
        // A UBL invoice should not include the InvoiceLine Item TransactionConditions


        // UBL-CR-596 warning
        // A UBL invoice should not include the InvoiceLine Item HazardousItem


        // UBL-CR-597 warning
        // A UBL invoice should not include the InvoiceLine Item ClassifiedTaxCategory Name


        // UBL-CR-598 warning
        // A UBL invoice should not include the InvoiceLine Item ClassifiedTaxCategory BaseUnitMeasure


        // UBL-CR-599 warning
        // A UBL invoice should not include the InvoiceLine Item ClassifiedTaxCategory PerUnitAmount


        // UBL-CR-600 warning
        // A UBL invoice should not include the InvoiceLine Item ClassifiedTaxCategory TaxExemptionReasonCode


        // UBL-CR-601 warning
        // A UBL invoice should not include the InvoiceLine Item ClassifiedTaxCategory TaxExemptionReason


        // UBL-CR-602 warning
        // A UBL invoice should not include the InvoiceLine Item ClassifiedTaxCategory TierRange


        // UBL-CR-603 warning
        // A UBL invoice should not include the InvoiceLine Item ClassifiedTaxCategory TierRatePercent


        // UBL-CR-604 warning
        // A UBL invoice should not include the InvoiceLine Item ClassifiedTaxCategory TaxScheme Name


        // UBL-CR-605 warning
        // A UBL invoice should not include the InvoiceLine Item ClassifiedTaxCategory TaxScheme TaxTypeCode


        // UBL-CR-606 warning
        // A UBL invoice should not include the InvoiceLine Item ClassifiedTaxCategory TaxScheme CurrencyCode


        // UBL-CR-607 warning
        // A UBL invoice should not include the InvoiceLine Item ClassifiedTaxCategory TaxScheme JurisdictionRegionAddress


        // UBL-CR-608 warning
        // A UBL invoice should not include the InvoiceLine Item AdditionalItemProperty ID


        // UBL-CR-609 warning
        // A UBL invoice should not include the InvoiceLine Item AdditionalItemProperty NameCode


        // UBL-CR-610 warning
        // A UBL invoice should not include the InvoiceLine Item AdditionalItemProperty TestMethod


        // UBL-CR-611 warning
        // A UBL invoice should not include the InvoiceLine Item AdditionalItemProperty ValueQuantity


        // UBL-CR-612 warning
        // A UBL invoice should not include the InvoiceLine Item AdditionalItemProperty ValueQualifier


        // UBL-CR-613 warning
        // A UBL invoice should not include the InvoiceLine Item AdditionalItemProperty ImportanceCode


        // UBL-CR-614 warning
        // A UBL invoice should not include the InvoiceLine Item AdditionalItemProperty ListValue


        // UBL-CR-615 warning
        // A UBL invoice should not include the InvoiceLine Item AdditionalItemProperty UsabilityPeriod


        // UBL-CR-616 warning
        // A UBL invoice should not include the InvoiceLine Item AdditionalItemProperty ItemPropertyGroup


        // UBL-CR-617 warning
        // A UBL invoice should not include the InvoiceLine Item AdditionalItemProperty RangeDimension


        // UBL-CR-618 warning
        // A UBL invoice should not include the InvoiceLine Item AdditionalItemProperty ItemPropertyRange


        // UBL-CR-619 warning
        // A UBL invoice should not include the InvoiceLine Item ManufacturerParty


        // UBL-CR-620 warning
        // A UBL invoice should not include the InvoiceLine Item InformationContentProviderParty


        // UBL-CR-621 warning
        // A UBL invoice should not include the InvoiceLine Item OriginAddress


        // UBL-CR-622 warning
        // A UBL invoice should not include the InvoiceLine Item ItemInstance


        // UBL-CR-623 warning
        // A UBL invoice should not include the InvoiceLine Item Certificate


        // UBL-CR-624 warning
        // A UBL invoice should not include the InvoiceLine Item Dimension


        // UBL-CR-625 warning
        // A UBL invoice should not include the InvoiceLine Item Price PriceChangeReason


        // UBL-CR-626 warning
        // A UBL invoice should not include the InvoiceLine Item Price PriceTypeCode


        // UBL-CR-627 warning
        // A UBL invoice should not include the InvoiceLine Item Price PriceType


        // UBL-CR-628 warning
        // A UBL invoice should not include the InvoiceLine Item Price OrderableUnitFactorRate


        // UBL-CR-629 warning
        // A UBL invoice should not include the InvoiceLine Item Price ValidityPeriod


        // UBL-CR-630 warning
        // A UBL invoice should not include the InvoiceLine Item Price PriceList


        // UBL-CR-632 warning
        // A UBL invoice should not include the InvoiceLine Item Price AllowanceCharge ID


        // UBL-CR-633 warning
        // A UBL invoice should not include the InvoiceLine Item Price AllowanceCharge AllowanceChargeReasonCode


        // UBL-CR-634 warning
        // A UBL invoice should not include the InvoiceLine Item Price AllowanceCharge AllowanceChargeReason


        // UBL-CR-635 warning
        // A UBL invoice should not include the InvoiceLine Item Price AllowanceCharge MultiplierFactorNumeric


        // UBL-CR-636 warning
        // A UBL invoice should not include the InvoiceLine Item Price AllowanceCharge PrepaidIndicator


        // UBL-CR-637 warning
        // A UBL invoice should not include the InvoiceLine Item Price AllowanceCharge SequenceNumeric


        // UBL-CR-638 warning
        // A UBL invoice should not include the InvoiceLine Item Price AllowanceCharge AccountingCostCode


        // UBL-CR-639 warning
        // A UBL invoice should not include the InvoiceLine Item Price AllowanceCharge AccountingCost


        // UBL-CR-640 warning
        // A UBL invoice should not include the InvoiceLine Item Price AllowanceCharge PerUnitAmount


        // UBL-CR-641 warning
        // A UBL invoice should not include the InvoiceLine Item Price AllowanceCharge TaxCategory


        // UBL-CR-642 warning
        // A UBL invoice should not include the InvoiceLine Item Price AllowanceCharge TaxTotal


        // UBL-CR-643 warning
        // A UBL invoice should not include the InvoiceLine Item Price AllowanceCharge PaymentMeans


        // UBL-CR-644 warning
        // A UBL invoice should not include the InvoiceLine Item Price PricingExchangeRate


        // UBL-CR-645 warning
        // A UBL invoice should not include the InvoiceLine DeliveryTerms


        // UBL-CR-646 warning
        // A UBL invoice should not include the InvoiceLine SubInvoiceLine


        // UBL-CR-647 warning
        // A UBL invoice should not include the InvoiceLine ItemPriceExtension


        // UBL-CR-648 warning
        // A UBL invoice should not include the CustomizationID scheme identifier


        // UBL-CR-649 warning
        // A UBL invoice should not include the ProfileID scheme identifier


        // UBL-CR-650 warning
        // A UBL invoice shall not include the Invoice ID scheme identifier


        // UBL-CR-651 warning
        // A UBL invoice should not include the SalesOrderID scheme identifier


        // UBL-CR-652 warning
        // A UBL invoice should not include the PartyTaxScheme CompanyID scheme identifier


        // UBL-CR-653 warning
        // A UBL invoice should not include the PaymentID scheme identifier


        // UBL-CR-654 warning
        // A UBL invoice should not include the PayeeFinancialAccount scheme identifier


        // UBL-CR-655 warning
        // A UBL invoice shall not include the FinancialInstitutionBranch ID scheme identifier


        // UBL-CR-656 warning
        // A UBL invoice should not include the InvoiceTypeCode listID


        // UBL-CR-657 warning
        // A UBL invoice should not include the DocumentCurrencyCode listID


        // UBL-CR-658 warning
        // A UBL invoice should not include the TaxCurrencyCode listID


        // UBL-CR-659 warning
        // A UBL invoice shall not include the AdditionalDocumentReference DocumentTypeCode listID


        // UBL-CR-660 warning
        // A UBL invoice should not include the Country Identification code listID


        // UBL-CR-661 warning
        // A UBL invoice should not include the PaymentMeansCode listID


        // UBL-CR-662 warning
        // A UBL invoice should not include the AllowanceChargeReasonCode listID


        // UBL-CR-663 warning
        // A UBL invoice should not include the unitCodeListID


        // UBL-CR-664 warning
        // A UBL invoice should not include the FinancialInstitutionBranch FinancialInstitution


        // UBL-CR-665 warning
        // A UBL invoice should not include the AdditionalDocumentReference ID schemeID unless the DocumentTypeCode equals '130'


        // UBL-CR-666 fatal
        // A UBL invoice shall not include an AdditionalDocumentReference simultaneously referring an Invoice Object Identifier and an Attachment


        // UBL-CR-667 warning
        // A UBL invoice should not include a Buyer Item Identification schemeID


        // UBL-CR-668 warning
        // A UBL invoice should not include a Sellers Item Identification schemeID


        // UBL-CR-669 warning
        // A UBL invoice should not include a Price Allowance Reason Code


        // UBL-CR-670 warning
        // A UBL invoice should not include a Price Allowance Reason


        // UBL-CR-671 warning
        // A UBL invoice should not include a Price Allowance Multiplier Factor


        // UBL-CR-672 warning
        // A UBL credit note should not include the CreditNoteTypeCode listID


        // UBL-CR-673 fatal
        // A UBL invoice shall not include an AdditionalDocumentReference simultaneously referring an Invoice Object Identifier and an Document Description


        // UBL-CR-674 warning
        // A UBL invoice should not include the PrimaryAccountNumber schemeID


        // UBL-CR-675 warning
        // A UBL invoice should not include the NetworkID schemeID


        // UBL-CR-676 warning
        // A UBL invoice should not include the PaymentMandate/ID schemeID


        // UBL-CR-677 warning
        // A UBL invoice should not include the PayerFinancialAccount/ID schemeID


        // UBL-CR-678 warning
        // A UBL invoice should not include the TaxCategory/ID schemeID


        // UBL-CR-679 warning
        // A UBL invoice should not include the ClassifiedTaxCategory/ID schemeID


        // UBL-CR-680 warning
        // A UBL invoice should not include the PaymentMeans/PayerFinancialAccount


        // UBL-CR-681 warning
        // A UBL invoice should not include the PaymentMeans InstructionNote


        // UBL-CR-682 warning
        // A UBL invoice should not include the Delivery DeliveryAddress


        // UBL-DT-01 fatal
        // Amounts shall be decimal up to two fraction digits


        // UBL-DT-06 fatal
        // Binary object elements shall contain the mime code attribute


        // UBL-DT-07 fatal
        // Binary object elements shall contain the file name attribute


        // UBL-DT-08 warning
        // Scheme name attribute should not be present


        // UBL-DT-09 warning
        // Scheme agency name attribute should not be present


        // UBL-DT-10 warning
        // Scheme data uri attribute should not be present


        // UBL-DT-11 warning
        // Scheme uri attribute should not be present


        // UBL-DT-12 warning
        // Format attribute should not be present


        // UBL-DT-13 warning
        // Unit code list identifier attribute should not be present


        // UBL-DT-14 warning
        // Unit code list agency identifier attribute should not be present


        // UBL-DT-15 warning
        // Unit code list agency name attribute should not be present


        // UBL-DT-16 warning
        // List agency name attribute should not be present


        // UBL-DT-17 warning
        // List name attribute should not be present


        // UBL-DT-18 warning
        // Name attribute should not be present


        // UBL-DT-19 warning
        // Language identifier attribute should not be present


        // UBL-DT-20 warning
        // List uri attribute should not be present


        // UBL-DT-21 warning
        // List scheme uri attribute should not be present


        // UBL-DT-22 warning
        // Language local identifier attribute should not be present


        // UBL-DT-23 warning
        // Uri attribute should not be present


        // UBL-DT-24 warning
        // Currency code list version id should not be present


        // UBL-DT-25 warning
        // CharacterSetCode attribute should not be present


        // UBL-DT-26 warning
        // EncodingCode attribute should not be present


        // UBL-DT-27 warning
        // Scheme Agency ID attribute should not be present


        // UBL-DT-28 warning
        // List Agency ID attribute should not be present


        // UBL-SR-01 fatal
        // Contract identifier shall occur maximum once.


        // UBL-SR-02 fatal
        // Receive advice identifier shall occur maximum once


        // UBL-SR-03 fatal
        // Despatch advice identifier shall occur maximum once


        // UBL-SR-04 fatal
        // Invoice object identifier shall occur maximum once


        // UBL-SR-05 fatal
        // Payment terms shall occur maximum once


        // UBL-SR-06 fatal
        // Preceding invoice reference shall occur maximum once


        // UBL-SR-07 fatal
        // If there is a preceding invoice reference, the preceding invoice number shall be present


        // UBL-SR-08 fatal
        // Invoice period shall occur maximum once


        // UBL-SR-09 fatal
        // Seller name shall occur maximum once


        // UBL-SR-10 fatal
        // Seller trader name shall occur maximum once


        // UBL-SR-11 fatal
        // Seller legal registration identifier shall occur maximum once


        // UBL-SR-12 fatal
        // Seller VAT identifier shall occur maximum once


        // UBL-SR-13 fatal
        // Seller tax registration shall occur maximum once


        // UBL-SR-14 fatal
        // Seller additional legal information shall occur maximum once


        // UBL-SR-15 fatal
        // Buyer name shall occur maximum once


        // UBL-SR-16 fatal
        // Buyer identifier shall occur maximum once


        // UBL-SR-17 fatal
        // Buyer legal registration identifier shall occur maximum once


        // UBL-SR-18 fatal
        // Buyer VAT identifier shall occur maximum once


        // UBL-SR-19 fatal
        // Payee name shall occur maximum once, if the Payee is different from the Seller


        // UBL-SR-20 fatal
        // Payee identifier shall occur maximum once, if the Payee is different from the Seller


        // UBL-SR-21 fatal
        // Payee legal registration identifier shall occur maximum once, if the Payee is different from the Seller


        // UBL-SR-22 fatal
        // Seller tax representative name shall occur maximum once, if the Seller has a tax representative


        // UBL-SR-23 fatal
        // Seller tax representative VAT identifier shall occur maximum once, if the Seller has a tax representative


        // UBL-SR-24 fatal
        // Deliver to information shall occur maximum once


        // UBL-SR-25 fatal
        // Deliver to party name shall occur maximum once


        // UBL-SR-26 fatal
        // Payment reference shall occur maximum once


        // UBL-SR-27 fatal
        // Payment means text shall occur maximum once


        // UBL-SR-28 fatal
        // Mandate reference identifier shall occur maximum once


        // UBL-SR-29 fatal
        // Bank creditor reference shall occur maximum once


        // UBL-SR-30 fatal
        // Document level allowance reason shall occur maximum once


        // UBL-SR-31 fatal
        // Document level charge reason shall occur maximum once


        // UBL-SR-32 fatal
        // VAT exemption reason text shall occur maximum once


        // UBL-SR-33 fatal
        // Supporting document description shall occur maximum once


        // UBL-SR-34 fatal
        // Invoice line note shall occur maximum once


        // UBL-SR-35 fatal
        // Referenced purchase order line identifier shall occur maximum once


        // UBL-SR-36 fatal
        // Invoice line period shall occur maximum once


        // UBL-SR-37 fatal
        // Item price discount shall occur maximum once


        // UBL-SR-39 fatal
        // Project reference shall occur maximum once.


        // UBL-SR-40 fatal
        // Buyer trade name shall occur maximum once


        // UBL-SR-42 fatal
        // Party tax scheme shall occur maximum twice in accounting supplier party


        // UBL-SR-43 fatal
        // Scheme identifier shall only be used for invoiced object (document type code with value 130 or 50)


        // UBL-SR-44 fatal
        // An Invoice may only have one unique PaymentID, but the PaymentID may be used for multiple PaymentMeans


        // UBL-SR-45 fatal
        // Due Date shall occur maximum once


        // UBL-SR-46 fatal
        // Payment means text shall occur maximum once


        // UBL-SR-47 fatal
        // When there are more than one payment means code, they shall be equal


        // UBL-SR-48 fatal
        // Invoice lines shall have one and only one classified tax category.


        // UBL-SR-49 fatal
        // Value tax point date shall occur maximum once


        // UBL-SR-50 fatal
        // Item description shall occur maximum once


        // UBL-SR-51 fatal
        // An address can only have one third line.


        // UBL-SR-52 fatal
        // Document reference shall occur maximum once


        // UBL-SR-53 fatal
        // CompanyID (VAT Identifier) must be stated when providing the PartyTaxScheme/TaxScheme/ID.


        // UBL-SR-54 fatal
        // An Invoice shall contain maximum one Payment Card account (BG-18).


        // UBL-SR-55 fatal
        // An Invoice shall contain maximum one Payment Mandate (BG-19).
    }
}