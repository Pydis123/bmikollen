<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Core\Crypto;

final class CryptoTest extends TestCase
{
    public function testEncryptDecryptRoundTrip(): void
    {
        $key = base64_encode(random_bytes(32));
        $crypto = new Crypto($key);
        $plain = '72.55';
        $enc = $crypto->encrypt($plain);
        $this->assertArrayHasKey('ciphertext', $enc);
        $this->assertArrayHasKey('iv', $enc);
        $this->assertArrayHasKey('tag', $enc);
        $out = $crypto->decrypt($enc['ciphertext'], $enc['iv'], $enc['tag']);
        $this->assertSame($plain, $out);
    }

    public function testDifferentCiphertextForSamePlaintext(): void
    {
        $key = base64_encode(random_bytes(32));
        $crypto = new Crypto($key);
        $plain = '70.00';
        $a = $crypto->encrypt($plain);
        $b = $crypto->encrypt($plain);
        $this->assertNotSame($a['ciphertext'], $b['ciphertext']);
        $this->assertNotSame($a['iv'], $b['iv']);
        $this->assertNotSame($a['tag'], $b['tag']);
    }

    public function testTamperDetection(): void
    {
        $key = base64_encode(random_bytes(32));
        $crypto = new Crypto($key);
        $plain = '65.0';
        $enc = $crypto->encrypt($plain);
        $tampered = $enc['ciphertext'] ^ "\x01"; // flip one bit
        $out = $crypto->decrypt($tampered, $enc['iv'], $enc['tag']);
        $this->assertFalse($out);
    }
}
