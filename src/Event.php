<?php

namespace Nuke;

// TODO: Event naming?
// TODO: Event token/state naming.

use Nuke\Events\WebhookNukeEvent;
use Nuke\Events\WebhookRevokeEvent;
use Nuke\Events\WebhookVerificationEvent;
use Nuke\Events\BrowserAuthorizeEvent;
use Nuke\Exceptions\MissingEventPropertyException;

class Event
{
    /**
     * Construct event
     *
     * @param array|null $data
     * @return WebhookVerificationEvent|WebhookRevokeEvent|WebhookNukeEvent|BrowserAuthorizeEvent
     * @throws MissingEventPropertyException
     */
    public static function construct(
        ?array $data
    ): WebhookVerificationEvent|WebhookRevokeEvent|WebhookNukeEvent|BrowserAuthorizeEvent {
        switch ($data['event']['type'] ?? $data['type'] ?? null) {
            case WebhookVerificationEvent::getName():
                $event = (new WebhookVerificationEvent());
                $event->token = ($data['event']['data']['token'] ?? null);
                return $event;

            case WebhookRevokeEvent::getName():
                $event = (new WebhookRevokeEvent());
                $event->token = ($data['event']['data']['token'] ?? null);
                return $event;

            case WebhookNukeEvent::getName():
                $event = (new WebhookNukeEvent());
                $event->token = ($data['event']['data']['token'] ?? null);
                $event->actions = ($data['event']['data']['actions'] ?? null);
                return $event;

            case BrowserAuthorizeEvent::getName():
                $event = (new BrowserAuthorizeEvent());
                $event->redirect_uri = ($data['redirect_uri'] ?? null);
                $event->state = ($data['state'] ?? null);
                $event->nuke_identifier = ($data['nuke_identifier'] ?? null);
                return $event;

            default:
                throw new MissingEventPropertyException('event.type');
        }
    }
}
