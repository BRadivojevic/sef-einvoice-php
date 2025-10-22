<?php
namespace App\Shared;

final class Http {
	public static function json(array $data, int $code = 200): void {
		http_response_code($code);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}
	public static function postJson(string $url, array $headers, array $payload): array {
		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array_merge(['Content-Type: application/json'], $headers),
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($payload)
		]);
		$body = curl_exec($ch);
		$err  = curl_error($ch);
		$code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
		curl_close($ch);
		return [$code, $body, $err];
	}
}
