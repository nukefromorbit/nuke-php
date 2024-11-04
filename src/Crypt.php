<?php

namespace Nuke;

use Exception;

use Nuke\Exceptions\CryptDecryptException;
use Nuke\Exceptions\CryptEncryptException;
use Nuke\Exceptions\CryptInvalidKeyException;
use Nuke\Exceptions\CryptInvalidPayloadException;

class Crypt
{
    private const CIPHER = 'aes-256-cbc';

    private const SIZE = 32;

    private const HASH_ALGO = 'sha256';

    /**
     * Encrypt the given value
     *
     * @param mixed $value
     * @param string $key
     * @return string
     * @throws Exception
     * @throws CryptEncryptException
     * @throws CryptInvalidKeyException
     */
    public static function encrypt(#[\SensitiveParameter] mixed $value, #[\SensitiveParameter] string $key): string
    {
        $key = hex2bin($key);

        if (!self::isKeyValid($key)) {
            throw new CryptInvalidKeyException();
        }

        $iv = random_bytes(openssl_cipher_iv_length(self::CIPHER));

        $encrypted = openssl_encrypt(
            $value,
            self::CIPHER,
            $key,
            0,
            $iv
        );

        if ($encrypted === false) {
            throw new CryptEncryptException();
        }

        $iv = base64_encode($iv);

        $mac = self::hash($iv, $encrypted, $key);

        $json = json_encode(['iv' => $iv, 'value' => $encrypted, 'mac' => $mac,], JSON_UNESCAPED_SLASHES);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new CryptEncryptException();
        }

        return base64_encode($json);
    }

    /**
     * Decrypt the given value
     *
     * @param string $payload
     * @param string $key
     * @return string
     * @throws CryptDecryptException
     * @throws CryptInvalidKeyException
     * @throws CryptInvalidPayloadException
     */
    public static function decrypt(string $payload, #[\SensitiveParameter] string $key): string
    {
        $key = hex2bin($key);

        if (!self::isKeyValid($key)) {
            throw new CryptInvalidKeyException();
        }

        $payload = self::getJsonPayload($payload);

        $iv = base64_decode($payload['iv']);

        if (!self::isMacValid($payload, $key)) {
            throw new CryptDecryptException();
        }

        $decrypted = openssl_decrypt(
            $payload['value'],
            self::CIPHER,
            $key,
            0,
            $iv
        );

        if ($decrypted === false) {
            throw new CryptDecryptException();
        }

        return $decrypted;
    }

    /**
     * Create MAC
     *
     * @param string $iv
     * @param mixed $value
     * @param string $key
     * @return string
     */
    protected static function hash(
        #[\SensitiveParameter] string $iv,
        #[\SensitiveParameter] mixed $value,
        #[\SensitiveParameter] string $key
    ): string {
        return hash_hmac(self::HASH_ALGO, $iv . $value, $key);
    }

    /**
     * Get JSON payload
     *
     * @param string $payload
     * @return array
     * @throws CryptInvalidPayloadException
     */
    protected static function getJsonPayload(string $payload): array
    {
        $payload = json_decode(base64_decode($payload), true);

        if (!self::isPayloadValid($payload)) {
            throw new CryptInvalidPayloadException();
        }

        return $payload;
    }

    /**
     * Check if the key is valid
     *
     * @param mixed $key
     * @return bool
     */
    protected static function isKeyValid(#[\SensitiveParameter] mixed $key): bool
    {
        return is_string($key) && mb_strlen($key, '8bit') === self::SIZE;
    }

    /**
     * Check if the payload is valid
     *
     * @param mixed $payload
     * @return bool
     */
    protected static function isPayloadValid(#[\SensitiveParameter] mixed $payload): bool
    {
        foreach (['iv', 'value', 'mac',] as $key) {
            if (!is_string($payload[$key] ?? null)) {
                return false;
            }
        }

        return strlen(base64_decode($payload['iv'], true)) === openssl_cipher_iv_length(self::CIPHER);
    }

    /**
     * Check if the MAC is valid
     *
     * @param array $payload
     * @param string $key
     * @return bool
     */
    protected static function isMacValid(
        #[\SensitiveParameter] array $payload,
        #[\SensitiveParameter] string $key
    ): bool {
        return hash_equals(
            self::hash($payload['iv'], $payload['value'], $key),
            $payload['mac']
        );
    }
}
