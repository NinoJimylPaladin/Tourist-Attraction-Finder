<?php

namespace TouristAttractionFinder\Infrastructure\Services;

class Logger
{
  private string $logFile;

  public function __construct(string $logFile = 'logs/app.log')
  {
    $this->logFile = $logFile;

    // Ensure log directory exists
    $logDir = dirname($logFile);
    if (!file_exists($logDir)) {
      mkdir($logDir, 0755, true);
    }
  }

  public function info(string $message, array $context = []): void
  {
    $this->log('INFO', $message, $context);
  }

  public function warning(string $message, array $context = []): void
  {
    $this->log('WARNING', $message, $context);
  }

  public function error(string $message, array $context = []): void
  {
    $this->log('ERROR', $message, $context);
  }

  public function debug(string $message, array $context = []): void
  {
    $this->log('DEBUG', $message, $context);
  }

  private function log(string $level, string $message, array $context): void
  {
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = $context ? ' ' . json_encode($context) : '';
    $logEntry = "[$timestamp] $level: $message{$contextStr}\n";

    file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
  }
}
