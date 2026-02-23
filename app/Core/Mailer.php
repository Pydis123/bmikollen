<?php
namespace App\Core;

class Mailer {
    private string $driver;
    private array $from;
    private Logger $logger;

    public function __construct(string $driver, array $from, Logger $logger) {
        $this->driver = $driver;
        $this->from = $from;
        $this->logger = $logger;
    }

    public function send(string $to, string $subject, string $body): bool {
        if ($this->driver === 'log') {
            $this->logger->info("Sending email to $to: [$subject] $body");
            return true;
        }

        $headers = "From: {$this->from['name']} <{$this->from['address']}>\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        return mail($to, $subject, $body, $headers);
    }
}
