<?php
namespace App\Core;

class Request {
    public function getMethod(): string {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getUri(): string {
        $uri = $_SERVER['REQUEST_URI'];
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        return rawurldecode($uri);
    }

    public function all(): array {
        return array_merge($_GET, $_POST);
    }

    public function input(string $key, $default = null) {
        $data = $this->all();
        return $data[$key] ?? $default;
    }

    public function isPost(): bool {
        return $this->getMethod() === 'POST';
    }

    public function getIp(): string {
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }
}
