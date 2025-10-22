<?php
namespace App\Serbia;
use App\Shared\Env;
use App\Shared\Http;
use App\Shared\XmlBuilder;

final class IssueInvoice {
	public static function send(array $payload): void {
		$xml = XmlBuilder::invoice($payload);
		$url = rtrim(Env::get('SEF_RS_BASE_URL'),'/').'/invoices';
		[$status, $body, $err] = Http::postJson($url, [
			'Authorization: Bearer '.Env::get('SEF_RS_API_KEY')
		], ['xml' => base64_encode($xml)]);
		Http::json(['status'=>$status, 'err'=>$err, 'raw'=>json_decode($body, true), 'xml'=>$xml]);
	}
}
