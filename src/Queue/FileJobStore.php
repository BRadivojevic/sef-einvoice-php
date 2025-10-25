<?php
namespace App\Queue;
final class FileJobStore implements JobStoreInterface {
  private string $file;
  public function __construct(string $file){ $this->file=$file; if(!is_dir(dirname($file)))@mkdir(dirname($file),0777,true); if(!file_exists($file))@touch($file); }
  public function enqueue(array $job): string{
    $id=bin2hex(random_bytes(8)); $job['id']=$id; $job['status']='queued'; $job['created_at']=date('c');
    file_put_contents($this->file,json_encode($job)."\n",FILE_APPEND); return $id; }
  public function nextQueued(): ?array{
    $lines=@file($this->file, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES)?:[];
    foreach($lines as $ln){ $r=json_decode($ln,true); if(($r['status']??'')==='queued') return $r; } return null; }
  private function rewrite(callable $mut){ $lines=@file($this->file, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES)?:[]; $rows=[]; foreach($lines as $ln){ $rows[]=json_decode($ln,true); } $rows=$mut($rows); $tmp=$this->file.'.tmp'; $fh=fopen($tmp,'w'); foreach($rows as $r){ fwrite($fh,json_encode($r)."\n"); } fclose($fh); rename($tmp,$this->file); }
  public function markRunning(string $id): void{ $this->rewrite(function($rows) use($id){ foreach($rows as &$r){ if(($r['id']??'')===$id){ $r['status']='running'; $r['started_at']=date('c'); } } return $rows; }); }
  public function markFinished(string $id, array $meta=[]): void{ $this->rewrite(function($rows) use($id,$meta){ foreach($rows as &$r){ if(($r['id']??'')===$id){ $r['status']='finished'; $r['finished_at']=date('c'); $r['meta']=$meta; } } return $rows; }); }
  public function markFailed(string $id, string $error): void{ $this->rewrite(function($rows) use($id,$error){ foreach($rows as &$r){ if(($r['id']??'')===$id){ $r['status']='failed'; $r['finished_at']=date('c'); $r['last_error']=$error; } } return $rows; }); }
  public function get(string $id): ?array{ $lines=@file($this->file, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES)?:[]; foreach($lines as $ln){ $r=json_decode($ln,true); if(($r['id']??'')===$id) return $r; } return null; }
}
