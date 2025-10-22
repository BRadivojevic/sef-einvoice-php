<?php
namespace App\Shared;

final class XmlBuilder {
	public static function invoice(array $data): string {
		$doc = new \DOMDocument('1.0', 'UTF-8');
		$root = $doc->createElement('Invoice');
		$doc->appendChild($root);

		$root->appendChild($doc->createElement('Id', htmlspecialchars($data['id'] ?? 'INV-0001')));
		$root->appendChild($doc->createElement('IssueDate', htmlspecialchars($data['date'] ?? date('Y-m-d'))));
		$root->appendChild($doc->createElement('BuyerName', htmlspecialchars($data['buyer'] ?? 'Buyer d.o.o.')));
		$root->appendChild($doc->createElement('Total', number_format((float)($data['total'] ?? 0), 2, '.', '')));

		return $doc->saveXML();
	}
}
