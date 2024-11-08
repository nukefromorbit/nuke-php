<?php

namespace Nuke\Events;

use Nuke\Nuke;
use Nuke\Exceptions\CryptDecryptException;
use Nuke\Exceptions\CryptInvalidKeyException;
use Nuke\Exceptions\CryptInvalidPayloadException;
use Nuke\Exceptions\MissingSecretException;
use Nuke\Exceptions\InvalidEventPropertyException;

class BrowserAuthorizeEvent extends AbstractEvent
{
    public const SOURCE_NUKE = 'nuke';

    public const SOURCE_SERVICE = 'service';

    private const TYPE = 'authorize';

    /**
     * @var string|null
     */
    public ?string $nuke_identifier = null;

    /**
     * @var string|null
     */
    public ?string $nuke_token = null;

    /**
     * @var string|null
     */
    public ?string $redirect_uri = null;

    /**
     * @var string|null
     */
    public ?string $source = null;

    /**
     * Construct
     *
     * @param array $data
     * @throws InvalidEventPropertyException
     * @throws CryptDecryptException
     * @throws CryptInvalidKeyException
     * @throws CryptInvalidPayloadException
     * @throws MissingSecretException
     */
    public function __construct(array $data)
    {
        if (($data['type'] ?? null) !== self::getType()) {
            throw new InvalidEventPropertyException('type');
        }

        if (
            !in_array(
                ($data['source'] ?? null),
                [
                    self::SOURCE_NUKE,
                    self::SOURCE_SERVICE
                ],
                true
            )
        ) {
            throw new InvalidEventPropertyException('source');
        }

        // This is a GET request. We need to decrypt the nuke token to validate the authenticity of the request.
        // Because of that it can throw exceptions.
        Nuke::decrypt(($data['nuke_token'] ?? null));

        $this->source = ($data['source'] ?? null);
        $this->nuke_identifier = ($data['nuke_identifier'] ?? null);
        $this->nuke_token = ($data['nuke_token'] ?? null);
        $this->redirect_uri = ($data['redirect_uri'] ?? null);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return self::TYPE;
    }
}
