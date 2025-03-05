<?php

namespace DMT\Ubl\Service\Entity\Invoice;

use DMT\Ubl\Service\Entity\Invoice\Type\EndpointId;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Until;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlRoot;

#[XmlRoot(
    name: "Party",
    namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
)]
class Party
{
    #[SerializedName(name: "EndpointID")]
    #[Since(version: "1.1")]
    #[Type(name: EndpointId::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2")]
    public null|EndpointId $endpointId = null;

    #[SerializedName(name: "PartyIdentification")]
    #[Since(version: "1.1")]
    #[Type(name: PartyIdentification::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|PartyIdentification $partyIdentification = null;

    #[SerializedName(name: "PartyName")]
    #[Type(name: PartyName::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|PartyName $partyName = null;

    #[SerializedName(name: "PostalAddress")]
    #[Type(name: Address::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|Address $postalAddress = null;

    #[SerializedName(name: "PartyLegalEntity")]
    #[Type(name: PartyLegalEntity::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|PartyLegalEntity $partyLegalEntity = null;

    #[SerializedName(name: "Contact")]
    #[Type(name: Contact::class)]
    #[XmlElement(cdata: false, namespace: "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2")]
    public null|Contact $contact = null;
}
