<?php
require_once __DIR__.'/../vendor/autoload.php';
use App\Config\Config;
use App\Logger\LoggerFactory;
use App\Queue\FileJobStore;
header('Content-Type: application/json');
$root = dirname(__DIR__);
$cfg = new Config($root);
$logger = LoggerFactory::make('http', $cfg->logPath());
$raw = file_get_contents('php://input'); $payload = json_decode($raw, true) ?: [];
$job = ['type'=>'send_invoice','payload'=>$payload];
try{
  $store = new FileJobStore($cfg->jobsFile());
  $id = $store->enqueue($job);
  echo json_encode(['job_id'=>$id]);
}catch(Throwable $e){ http_response_code(500); echo json_encode(['error'=>$e->getMessage()]); }
