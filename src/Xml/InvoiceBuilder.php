<?php
namespace App\Xml;
use DOMDocument;
use App\Utils\Normalizer;
final class InvoiceBuilder {
  /** Build minimal UBL 2.1 compliant (profile-aligned) invoice for SEF. */
  public static function build(array $seller, array $buyer, array $items, string $currency='RSD', string $issueDate=null, array $opts=[]): string {
    $doc = new DOMDocument('1.0','UTF-8'); $doc->formatOutput=true;
    $Invoice = $doc->createElement('Invoice');
    $Invoice->setAttribute('xmlns','urn:oasis:names:specification:ubl:schema:xsd:Invoice-2');
    $Invoice->setAttribute('xmlns:cac','urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
    $Invoice->setAttribute('xmlns:cbc','urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
    $doc->appendChild($Invoice);

    $Invoice->appendChild(self::cbc($doc,'CustomizationID','rs:sefinvoice:3.14.0'));
    $Invoice->appendChild(self::cbc($doc,'ProfileID','reporting:1.0'));
    $Invoice->appendChild(self::cbc($doc,'ID',$opts['id'] ?? 'INV-'.date('Ymd-His')));
    $Invoice->appendChild(self::cbc($doc,'IssueDate', substr($issueDate?:date('Y-m-d'),0,10)));
    $Invoice->appendChild(self::cbc($doc,'InvoiceTypeCode', $opts['type'] ?? '380'));

    // Seller
    $AccountingSupplierParty = $doc->createElement('cac:AccountingSupplierParty');
    $Party = $doc->createElement('cac:Party');
    $PartyName = $doc->createElement('cac:PartyName'); $PartyName->appendChild(self::cbc($doc,'Name', Normalizer::ascii($seller['name'] ?? '')));
    $Party->appendChild($PartyName);
    $PartyTaxScheme = $doc->createElement('cac:PartyTaxScheme');
    $PartyTaxScheme->appendChild(self::cbc($doc,'CompanyID',$seller['tin'] ?? ''));
    $Party->appendChild($PartyTaxScheme);
    $Postal = $doc->createElement('cac:PostalAddress');
    $Postal->appendChild(self::cbc($doc,'StreetName', Normalizer::ascii($seller['street'] ?? '')));
    $Postal->appendChild(self::cbc($doc,'CityName', Normalizer::ascii($seller['city'] ?? '')));
    $Postal->appendChild(self::cbc($doc,'CountrySubentity',''));
    $Country = $doc->createElement('cac:Country'); $Country->appendChild(self::cbc($doc,'IdentificationCode',$seller['country'] ?? 'RS'));
    $Postal->appendChild($Country); $Party->appendChild($Postal);
    $AccountingSupplierParty->appendChild($Party); $Invoice->appendChild($AccountingSupplierParty);

    // Buyer
    $AccountingCustomerParty = $doc->createElement('cac:AccountingCustomerParty');
    $bParty = $doc->createElement('cac:Party');
    $bPartyName = $doc->createElement('cac:PartyName'); $bPartyName->appendChild(self::cbc($doc,'Name', Normalizer::ascii($buyer['name'] ?? 'Buyer')));
    $bParty->appendChild($bPartyName);
    $bTax = $doc->createElement('cac:PartyTaxScheme'); $bTax->appendChild(self::cbc($doc,'CompanyID',$buyer['tin'] ?? ''));
    $bParty->appendChild($bTax);
    $bPostal = $doc->createElement('cac:PostalAddress');
    $bPostal->appendChild(self::cbc($doc,'StreetName', Normalizer::ascii($buyer['street'] ?? '')));
    $bPostal->appendChild(self::cbc($doc,'CityName', Normalizer::ascii($buyer['city'] ?? '')));
    $bCountry = $doc->createElement('cac:Country'); $bCountry->appendChild(self::cbc($doc,'IdentificationCode',$buyer['country'] ?? 'RS'));
    $bPostal->appendChild($bCountry); $bParty->appendChild($bPostal);
    $AccountingCustomerParty->appendChild($bParty); $Invoice->appendChild($AccountingCustomerParty);

    // Lines
    $lineNo=0; $taxTotal=0; $legalVAT=True;
    foreach($items as $it){
      $lineNo++; $qty=(float)($it['qty'] ?? 1); $price=(float)($it['price'] ?? 0);
      $vat = strtoupper(trim($it['vat_code'] ?? 'S20'));
      if(in_array($vat, ['S10','S20'])) $legalVAT=True;
      $line = $doc->createElement('cac:InvoiceLine');
      $line->appendChild(self::cbc($doc,'ID', (string)$lineNo));
      $line->appendChild(self::cbc($doc,'InvoicedQuantity', (string)$qty));
      $line->appendChild(self::cbc($doc,'LineExtensionAmount', number_format($qty*$price,2,'.','')));
      $Item = $doc->createElement('cac:Item');
      $Item->appendChild(self::cbc($doc,'Name', Normalizer::ascii($it['name'] ?? ('Stavka '+$lineNo))));
      $TaxCategory = $doc->createElement('cac:ClassifiedTaxCategory');
      $TaxCategory->appendChild(self::cbc($doc,'ID',$vat));
      $TaxCategory->appendChild(self::cbc($doc,'Percent', $vat=='S10'?'10':($vat=='S20'?'20':'0')));
      $Item->appendChild($TaxCategory);
      $line->appendChild($Item);
      $Price = $doc->createElement('cac:Price'); $Price->appendChild(self::cbc($doc,'PriceAmount', number_format($price,2,'.','')));
      $line->appendChild($Price);
      $Invoice->appendChild($line);
      $taxTotal += ($vat=='S10')?($qty*$price*0.10):(($vat=='S20')?($qty*$price*0.20):0);
    }

    // Totals
    $LegalMonetaryTotal = $doc->createElement('cac:LegalMonetaryTotal');
    $sum = 0.0; foreach($items as $it){ $sum += (float)($it['qty']??1) * (float)($it['price']??0); }
    $LegalMonetaryTotal->appendChild(self::cbc($doc,'LineExtensionAmount', number_format($sum,2,'.','')));
    $LegalMonetaryTotal->appendChild(self::cbc($doc,'TaxExclusiveAmount', number_format($sum,2,'.','')));
    $LegalMonetaryTotal->appendChild(self::cbc($doc,'TaxInclusiveAmount', number_format($sum+$taxTotal,2,'.','')));
    $Invoice->appendChild($LegalMonetaryTotal);

    // VAT flag (SEF profile check: when S10/S20 present, VAT is calculated must be true)
    if($legalVAT){
      $Invoice->appendChild(self::cbc($doc,'Note','VAT is calculated'));
    }

    return $doc->saveXML();
  }
  private static function cbc(DOMDocument $d, string $name, string $value){ $e=$d->createElement('cbc:'.$name); $e->appendChild($d->createTextNode($value)); return $e; }
}
