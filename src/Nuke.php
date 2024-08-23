<?php

namespace Nuke;

use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\BadFormatException;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;

use Nuke\Exceptions\MissingSecretException;
use Nuke\Exceptions\MissingIdentifierException;

// TODO: identifier and secret private?
// TODO: PHP-Defuse is specific to PHP. We need to make sure that the encrypted value can also be decrypted with other
//  programming Languages. Or create our own little encryption library?

class Nuke
{
    private const HEADER_NUKE_SIGNATURE_ALGORITHM = 'sha256';
    private const HEADER_NUKE_SIGNATURE_VALUE_FORMAT = 't=%1$d, v=%2$s';
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
    public static ?string $secret = null;

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
    public static function setSecret(?string $secret): void
    {
        self::$secret = $secret;
    }

    /**
     * Encrypt using the Nuke Secret
     *
     * @param string $value
     * @return string
     * @throws BadFormatException
     * @throws EnvironmentIsBrokenException
     */
    public static function encrypt(string $value): string
    {
        return Crypto::encrypt(
            $value,
            Key::loadFromAsciiSafeString(self::$secret)
        );
    }

    /**
     * Decrypt using the Nuke Secret
     *
     * @param string $value
     * @return string
     * @throws BadFormatException
     * @throws EnvironmentIsBrokenException
     * @throws WrongKeyOrModifiedCiphertextException
     */
    public static function decrypt(string $value): string
    {
        return Crypto::decrypt(
            $value,
            Key::loadFromAsciiSafeString(self::$secret)
        );
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
     * Verify missing Nuke Identifier
     *
     * @return void
     * @throws MissingIdentifierException
     */
    private static function verifyMissingIdentifier(): void
    {
        if (!(is_string(self::$secret) && strlen(self::$secret))) {
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
