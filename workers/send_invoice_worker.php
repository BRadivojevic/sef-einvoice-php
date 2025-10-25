<?php
require_once __DIR__.'/../vendor/autoload.php';
use App\Config\Config;
use App\Logger\LoggerFactory;
use App\Queue\FileJobStore;
use App\Http\SefClient;
use App\Services\EracunService;

$root = dirname(__DIR__);
$cfg = new Config($root);
$logger = LoggerFactory::make('worker.sef', $cfg->logPath());
$store = new FileJobStore($cfg->jobsFile());

$job = $store->nextQueued(); if(!$job){ $logger->info('no.jobs'); exit(0); }
$store->markRunning($job['id']);
$payload = $job['payload']['payload'] ?? $job['payload'] ?? [];

try{
  $client = new SefClient($cfg->sefBase(), $cfg->sefApiKey(), $cfg->sefZrn(), $logger);
  $svc = new EracunService($client, $logger);
  $res = $svc->buildAndSubmit($payload);
  $store->markFinished($job['id'], ['submit'=>$res['submit']]);
  $logger->info('job.done', ['job_id'=>$job['id']]);
}catch(Throwable $e){
  $store->markFailed($job['id'], $e->getMessage());
  @file_put_contents($cfg->deadLetterFile(), json_encode(['job'=>$job,'error'=>$e->getMessage()])."\n", FILE_APPEND);
  $logger->error('job.fail', ['job_id'=>$job['id'], 'error'=>$e->getMessage()]);
  exit(1);
}
