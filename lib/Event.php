<?php

namespace Nuke;

use Nuke\Events\WebhookNukeEvent;
use Nuke\Events\WebhookRevokeEvent;
use Nuke\Events\WebhookAuthorizeEvent;
use Nuke\Events\BrowserAuthorizeEvent;
use Nuke\Exceptions\CryptDecryptException;
use Nuke\Exceptions\CryptInvalidKeyException;
use Nuke\Exceptions\CryptInvalidPayloadException;
use Nuke\Exceptions\InvalidIdentifierException;
use Nuke\Exceptions\InvalidSignatureException;
use Nuke\Exceptions\MissingIdentifierException;
use Nuke\Exceptions\MissingSecretException;
use Nuke\Exceptions\InvalidEventClassException;
use Nuke\Exceptions\InvalidEventPropertyException;

class Event
{
    /**
     * Construct event
     * This will validate the request authenticity.
     *
     * @param class-string<WebhookAuthorizeEvent|WebhookRevokeEvent|WebhookNukeEvent|BrowserAuthorizeEvent> $class
     * @return WebhookAuthorizeEvent|WebhookRevokeEvent|WebhookNukeEvent|BrowserAuthorizeEvent
     * @throws CryptDecryptException
     * @throws CryptInvalidKeyException
     * @throws CryptInvalidPayloadException
     * @throws InvalidIdentifierException
     * @throws InvalidSignatureException
     * @throws MissingIdentifierException
     * @throws MissingSecretException
     * @throws InvalidEventClassException
     * @throws InvalidEventPropertyException
     */
    public static function construct(
        string $class
    ): WebhookAuthorizeEvent|WebhookRevokeEvent|WebhookNukeEvent|BrowserAuthorizeEvent {
        return match ($class) {
            WebhookAuthorizeEvent::class => (new WebhookAuthorizeEvent($_POST)),
            WebhookRevokeEvent::class => (new WebhookRevokeEvent($_POST)),
            WebhookNukeEvent::class => (new WebhookNukeEvent($_POST)),
            BrowserAuthorizeEvent::class => (new BrowserAuthorizeEvent($_GET)),
            default => throw new InvalidEventClassException(),
        };
    }
}
