<?php

namespace Nuke;

use Nuke\Exceptions\MissingSecretException;
use Nuke\Exceptions\MissingIdentifierException;
use Nuke\Exceptions\InvalidSignatureException;
use Nuke\Exceptions\InvalidIdentifierException;
use Nuke\Exceptions\CryptEncryptException;
use Nuke\Exceptions\CryptDecryptException;
use Nuke\Exceptions\CryptInvalidKeyException;
use Nuke\Exceptions\CryptInvalidPayloadException;

class Nuke
{
    public const APP_URL = 'https://app.nuke.app';
    public const APP_AUTHORIZE_URL = 'https://app.nuke.app/authorize';

    public const HEADER_X_NUKE_IDENTIFIER = 'X-Nuke-Identifier';
    public const HEADER_X_NUKE_SIGNATURE = 'X-Nuke-Signature';

    private const HEADER_NUKE_SIGNATURE_ALGORITHM = 'sha256';
    private const HEADER_NUKE_SIGNATURE_VALUE_FORMAT = 't=%1$d, v=%2$s';
    private const HEADER_NUKE_SIGNATURE_VALUE_REGEX = '/^t=(\d+), v=([0-9a-zA-Z]+)$/';
    private const HEADER_NUKE_SIGNATURE_VALUE_PAYLOAD_FORMAT = '%1$d.%2$s';

    /**
     * Nuke Identifier
     *
     * @var string|null
     */
    public static ?string $identifier = null;

    /**
     * Nuke Secret
     *
     * @var string|null
     */
    private static ?string $secret = null;

    /**
     * Set Nuke Identifier
     *
     * @param string|null $identifier
     * @return void
     */
    public static function setIdentifier(?string $identifier): void
    {
        self::$identifier = $identifier;
    }

    /**
     * Set Nuke Secret
     *
     * @param string|null $secret
     * @return void
     */
    public static function setSecret(#[\SensitiveParameter] ?string $secret): void
    {
        self::$secret = $secret;
    }

    /**
     * Encrypt using the Nuke Secret
     *
     * @param string $value
     * @return string
     * @throws CryptInvalidKeyException
     * @throws CryptEncryptException
     * @throws MissingSecretException
     */
    public static function encrypt(#[\SensitiveParameter] string $value): string
    {
        self::verifyMissingSecret();

        return Crypt::encrypt($value, self::$secret);
    }

    /**
     * Decrypt using the Nuke Secret
     *
     * @param string $value
     * @return string
     * @throws CryptDecryptException
     * @throws CryptInvalidKeyException
     * @throws CryptInvalidPayloadException
     * @throws MissingSecretException
     */
    public static function decrypt(string $value): string
    {
        self::verifyMissingSecret();

        return Crypt::decrypt($value, self::$secret);
    }

    /**
     * Get (generate) Nuke Signature
     *
     * ```php
     * sprintf(
     *      't=%1$d, v=%2$s',
     *      $time,
     *      hash_hmac(
     *          'sha256',
     *          sprintf(
     *              '%1$d.%2$s',
     *              $time,
     *              json_encode($data)
     *          ),
     *          $nukeSecret
     *      )
     * );
     * ```
     *
     * @param int $time
     * @param array $data
     * @return string
     * @throws MissingSecretException
     */
    public static function getSignature(int $time, array $data): string
    {
        self::verifyMissingSecret();

        return sprintf(
            self::HEADER_NUKE_SIGNATURE_VALUE_FORMAT,
            $time,
            hash_hmac(
                self::HEADER_NUKE_SIGNATURE_ALGORITHM,
                sprintf(
                    self::HEADER_NUKE_SIGNATURE_VALUE_PAYLOAD_FORMAT,
                    $time,
                    json_encode($data)
                ),
                self::$secret
            ),
        );
    }

    /**
     * Verify identifier and signature
     *
     * @param string $identifier
     * @param string $signature
     * @param array $data
     * @return void
     * @throws InvalidIdentifierException
     * @throws InvalidSignatureException
     * @throws MissingIdentifierException
     * @throws MissingSecretException
     */
    public static function verifyIdentifierAndSignature(string $identifier, string $signature, array $data): void
    {
        self::verifyMissingIdentifier();

        if ($identifier !== self::$identifier) {
            throw new InvalidIdentifierException();
        }

        self::verifyMissingSecret();

        preg_match(self::HEADER_NUKE_SIGNATURE_VALUE_REGEX, $signature, $matches);
        if (
            !hash_equals(
                self::getSignature(
                    (int)($matches[1] ?? null),
                    $data
                ),
                $signature
            )
        ) {
            throw new InvalidSignatureException();
        }
    }

    /**
     * Verify missing Nuke Identifier
     *
     * @return void
     * @throws MissingIdentifierException
     */
    private static function verifyMissingIdentifier(): void
    {
        if (!(is_string(self::$identifier) && strlen(self::$identifier))) {
            throw new MissingIdentifierException();
        }
    }

    /**
     * Verify missing Nuke Secret
     *
     * @return void
     * @throws MissingSecretException
     */
    private static function verifyMissingSecret(): void
    {
        if (!(is_string(self::$secret) && strlen(self::$secret))) {
            throw new MissingSecretException();
        }
    }
}
