<?php
namespace App\Core;

class Logger {
    private string $logFile;

    public function __construct(string $logFile) {
        $this->logFile = $logFile;
        if (!file_exists(dirname($logFile))) {
            mkdir(dirname($logFile), 0777, true);
        }
    }

    public function info(string $message, array $context = []): void {
        $this->log('INFO', $message, $context);
    }

    public function security(string $message, array $context = []): void {
        $this->log('SECURITY', $message, $context);
    }

    public function error(string $message, array $context = []): void {
        $this->log('ERROR', $message, $context);
    }

    private function log(string $level, string $message, array $context): void {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logEntry = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
    }
}
