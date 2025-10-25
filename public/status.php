<?php
require_once __DIR__.'/../vendor/autoload.php';
use App\Config\Config;
use App\Queue\FileJobStore;
header('Content-Type: application/json');
$id = $_GET['id'] ?? ''; if($id===''){ http_response_code(400); echo json_encode(['error'=>'missing id']); exit; }
$cfg = new Config(dirname(__DIR__)); $store = new FileJobStore($cfg->jobsFile());
$row = $store->get($id); if(!$row){ http_response_code(404); echo json_encode(['error'=>'not found']); exit; }
echo json_encode($row);
