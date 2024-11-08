<?php

namespace Nuke\Events;

use Nuke\Nuke;
use Nuke\Exceptions\MissingSecretException;
use Nuke\Exceptions\MissingIdentifierException;
use Nuke\Exceptions\InvalidIdentifierException;
use Nuke\Exceptions\InvalidSignatureException;
use Nuke\Exceptions\InvalidEventPropertyException;

class WebhookNukeEvent extends AbstractEvent
{
    private const TYPE = 'nuke';

    /**
     * @var string|null
     */
    public ?string $token = null;

    /**
     * @param array $data
     * @throws InvalidEventPropertyException
     * @throws InvalidIdentifierException
     * @throws InvalidSignatureException
     * @throws MissingIdentifierException
     * @throws MissingSecretException
     */
    public function __construct(array $data)
    {
        // This is a POST request. We need to verify the nuke identifier and signature to validate the
        // authenticity of the request.
        // Because of that it can throw exceptions.
        Nuke::verifyIdentifierAndSignature(
            $_SERVER['HTTP_' . Nuke::HEADER_X_NUKE_IDENTIFIER] ?? '',
            $_SERVER['HTTP_' . Nuke::HEADER_X_NUKE_SIGNATURE] ?? '',
            $data
        );

        if (($data['event']['type'] ?? null) !== WebhookNukeEvent::getType()) {
            throw new InvalidEventPropertyException('event.type');
        }

        if (!is_string($data['event']['data']['token'] ?? null) || !strlen($data['event']['data']['token'])) {
            throw new InvalidEventPropertyException('event.data.token');
        }

        $this->token = $data['event']['data']['token'];

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
