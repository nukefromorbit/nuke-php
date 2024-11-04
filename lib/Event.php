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
        switch ($class) {
            case WebhookAuthorizeEvent::class:
                // This is a POST request. We need to verify the nuke identifier and signature to validate the
                // authenticity of the request.
                // Because of that it can throw exceptions.
                Nuke::verifyIdentifierAndSignature(
                    $_SERVER['HTTP_' . Nuke::HEADER_X_NUKE_IDENTIFIER],
                    $_SERVER['HTTP_' . Nuke::HEADER_X_NUKE_SIGNATURE],
                    $_POST
                );

                if (($_POST['event']['type'] ?? null) !== WebhookAuthorizeEvent::getType()) {
                    throw new InvalidEventPropertyException('event.type');
                }

                $event = (new WebhookAuthorizeEvent());
                $event->token = ($_POST['event']['data']['token'] ?? null);
                return $event;

            case WebhookRevokeEvent::class:
                // This is a POST request. We need to verify the nuke identifier and signature to validate the
                // authenticity of the request.
                // Because of that it can throw exceptions.
                Nuke::verifyIdentifierAndSignature(
                    $_SERVER['HTTP_' . Nuke::HEADER_X_NUKE_IDENTIFIER],
                    $_SERVER['HTTP_' . Nuke::HEADER_X_NUKE_SIGNATURE],
                    $_POST
                );

                if (($_POST['event']['type'] ?? null) !== WebhookRevokeEvent::getType()) {
                    throw new InvalidEventPropertyException('event.type');
                }

                $event = (new WebhookRevokeEvent());
                $event->token = ($_POST['event']['data']['token'] ?? null);
                return $event;

            case WebhookNukeEvent::class:
                // This is a POST request. We need to verify the nuke identifier and signature to validate the
                // authenticity of the request.
                // Because of that it can throw exceptions.
                Nuke::verifyIdentifierAndSignature(
                    $_SERVER['HTTP_' . Nuke::HEADER_X_NUKE_IDENTIFIER],
                    $_SERVER['HTTP_' . Nuke::HEADER_X_NUKE_SIGNATURE],
                    $_POST
                );

                if (($_POST['event']['type'] ?? null) !== WebhookNukeEvent::getType()) {
                    throw new InvalidEventPropertyException('event.type');
                }

                $event = (new WebhookNukeEvent());
                $event->token = ($_POST['event']['data']['token'] ?? null);
                return $event;

            case BrowserAuthorizeEvent::class:
                // This is a GET request. We need to decrypt the nuke token to validate the authenticity of the request.
                // Because of that it can throw exceptions.
                Nuke::decrypt(($_GET['nuke_token'] ?? null));

                $event = (new BrowserAuthorizeEvent());
                $event->nuke_identifier = ($_GET['nuke_identifier'] ?? null);
                $event->nuke_token = ($_GET['nuke_token'] ?? null);
                $event->redirect_uri = ($_GET['redirect_uri'] ?? null);
                return $event;

            default:
                throw new InvalidEventClassException();
        }
    }
}
