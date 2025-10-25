<?php
namespace App\Config;
use Dotenv\Dotenv;
final class Config {
  private array $env;
  public function __construct(string $rootDir) {
    if (is_file($rootDir.'/.env')) { $dotenv = Dotenv::createImmutable($rootDir); $dotenv->load(); }
    $this->env = $_ENV + $_SERVER;
  }
  public function env(string $k, $def=null){ return $this->env[$k]??$def; }
  public function logPath(): string { return $this->env['APP_LOG'] ?? __DIR__.'/../../var/app.log'; }
  public function jobStore(): string { return $this->env['JOB_STORE'] ?? 'file'; }
  public function jobsFile(): string { return $this->env['JOBS_FILE'] ?? __DIR__.'/../../var/jobs.jsonl'; }
  public function deadLetterFile(): string { return $this->env['DEADLETTER_FILE'] ?? __DIR__.'/../../var/deadletter.jsonl'; }
  public function sefBase(): string { return rtrim($this->env['SEF_BASE_URL'] ?? '', '/'); }
  public function sefApiKey(): string { return $this->env['SEF_API_KEY'] ?? ''; }
  public function sefZrn(): string { return $this->env['SEF_TENANT_ZRN'] ?? ''; }
  public function seller(): array { return ['name'=>$this->env['SELLER_NAME']??'Moja Firma d.o.o.','tin'=>$this->env['SELLER_TIN']??'123456789','city'=>$this->env['SELLER_CITY']??'Beograd','street'=>$this->env['SELLER_STREET']??'Ulica 1','country'=>$this->env['SELLER_COUNTRY']??'RS']; }
  public function currency(): string { return $this->env['CURRENCY'] ?? 'RSD'; }
}
