<?php
namespace App\Logger;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;
final class LoggerFactory {
  public static function make(string $name, string $path): Logger {
    if (!is_dir(dirname($path))) @mkdir(dirname($path), 0777, true);
    $log = new Logger($name);
    $h = new StreamHandler($path, Logger::INFO, true);
    $h->setFormatter(new JsonFormatter());
    $log->pushHandler($h);
    return $log;
  }
}
