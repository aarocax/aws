<?php

namespace METRIC\App\Service;

class SecureToken
{
    public static function generateToken(int $number): array
    {
        $key = random_bytes(16);
        $numBytes = pack('J', $number);
        $iv = random_bytes(openssl_cipher_iv_length('aes-256-gcm'));
        $encryptedHash = openssl_encrypt($numBytes, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
        $hash = base64_encode($iv . $encryptedHash . $tag);

        return array(
            'key' => base64_encode($key),
            'hash' => $hash
        );
    }

    public static function decodeToken(string $hash, string $key): int | null
    {
        $key = base64_decode($key);
        $data = base64_decode($hash);
        $iv = substr($data, 0, openssl_cipher_iv_length('aes-256-gcm'));
        $encryptedHash = substr($data, openssl_cipher_iv_length('aes-256-gcm'), -16);
        $tag = substr($data, -16);
        $numBytes = openssl_decrypt($encryptedHash, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
        $number = unpack('J', $numBytes)[1];

        return $number;
    }
}
