<?php
namespace App\Queue;
interface JobStoreInterface {
  public function enqueue(array $job): string;
  public function nextQueued(): ?array;
  public function markRunning(string $id): void;
  public function markFinished(string $id, array $meta=[]): void;
  public function markFailed(string $id, string $error): void;
  public function get(string $id): ?array;
}
