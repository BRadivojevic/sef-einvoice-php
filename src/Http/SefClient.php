<?php
namespace App\Http;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
final class SefClient {
  private Client $http; private LoggerInterface $logger; private string $base; private string $apiKey; private string $zrn;
  public function __construct(string $base, string $apiKey, string $zrn, LoggerInterface $logger){
    $this->base=rtrim($base,'/'); $this->apiKey=$apiKey; $this->zrn=$zrn; $this->logger=$logger;
    $this->http=new Client(['base_uri'=>$this->base,'timeout'=>30,'http_errors'=>False]);
  }
  private function headers(): array { return ['X-API-KEY'=>$this->apiKey,'Content-Type'=>'application/xml']; }
  public function submitInvoice(string $xml): array{
    $resp=$this->http->post('/api/public/v1/invoice', ['headers'=>$this->headers(),'body'=>$xml]);
    $code=$resp->getStatusCode(); $body=(string)$resp->getBody();
    $this->logger->info('sef.submit',{ 'code'=>$code });
    return ['status'=>$code,'body'=>$body];
  }
  public function getStatus(string $invoiceId): array{
    $resp=$this->http->get('/api/public/v1/invoice/status/'+$invoiceId, ['headers'=>$this->headers()]);
    return ['status'=>$resp->getStatusCode(),'body'=>(string)$resp->getBody()];
  }
}
