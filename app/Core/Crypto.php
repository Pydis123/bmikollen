<?php
namespace App\Core;

class Crypto {
    private string $key;

    public function __construct(string $base64Key) {
        if (str_starts_with($base64Key, 'base64:')) {
            $base64Key = substr($base64Key, 7);
        }
        $this->key = base64_decode($base64Key);
        if (strlen($this->key) !== 32) {
            throw new \Exception("Invalid master key length. Must be 32 bytes.");
        }
    }

    public function encrypt(string $data): array {
        $iv = random_bytes(16);
        $ciphertext = openssl_encrypt($data, 'aes-256-gcm', $this->key, OPENSSL_RAW_DATA, $iv, $tag);
        
        return [
            'ciphertext' => $ciphertext,
            'iv' => $iv,
            'tag' => $tag
        ];
    }

    public function decrypt(string $ciphertext, string $iv, string $tag): string|false {
        return openssl_decrypt($ciphertext, 'aes-256-gcm', $this->key, OPENSSL_RAW_DATA, $iv, $tag);
    }
}
