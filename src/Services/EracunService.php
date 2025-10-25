<?php
namespace App\Services;
use App\Xml\InvoiceBuilder;
use App\Http\SefClient;
use Psr\Log\LoggerInterface;

final class EracunService {
  private SefClient $client; private LoggerInterface $logger;
  public function __construct(SefClient $client, LoggerInterface $logger){ $this->client=$client; $this->logger=$logger; }

  public function buildAndSubmit(array $payload): array {
    $seller = $payload['seller'] ?? [];
    $buyer  = $payload['buyer'] ?? [];
    $items  = $payload['items'] ?? [];
    $currency = $payload['currency'] ?? 'RSD';
    $issue = $payload['issue_date'] ?? date('Y-m-d');
    $xml = InvoiceBuilder::build($seller, $buyer, $items, $currency, $issue);
    $this->logger->info('xml.built', ['bytes'=>strlen($xml)]);
    $resp = $this->client->submitInvoice($xml);
    return ['xml'=>$xml, 'submit'=>$resp];
  }
}
