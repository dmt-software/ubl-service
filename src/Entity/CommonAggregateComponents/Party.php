<?php

namespace DMT\Ubl\Service\Entity\CommonAggregateComponents;

use DMT\Ubl\Service\Entity\CommonBasicComponents\EndpointId;
use DMT\Ubl\Service\Entity\Versions;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "Party",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class Party implements CommonAggregateComponent
{
    #[SerializedName(name: "EndpointID")]
    #[Since(version: Versions::VERSION_1_1)]
    #[Type(name: EndpointId::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|EndpointId $endpointId = null;

    #[SerializedName(name: "PartyIdentification")]
    #[Since(version: Versions::VERSION_1_1)]
    #[Type(name: PartyIdentification::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|PartyIdentification $partyIdentification = null;

    #[SerializedName(name: "PartyName")]
    #[Type(name: PartyName::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|PartyName $partyName = null;

    #[SerializedName(name: "PostalAddress")]
    #[Type(name: PostalAddress::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|PostalAddress $postalAddress = null;

    #[SerializedName(name: "PartyTaxScheme")]
    #[Type(name: PartyTaxScheme::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|PartyTaxScheme $partyTaxScheme = null;

    #[SerializedName(name: "PartyLegalEntity")]
    #[Type(name: PartyLegal::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|PartyLegal $partyLegalEntity = null;

    #[SerializedName(name: "Contact")]
    #[Type(name: Contact::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|Contact $contact = null;
}
