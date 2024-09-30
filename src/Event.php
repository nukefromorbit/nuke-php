<?php

namespace Nuke;

use Nuke\Events\WebhookNukeEvent;
use Nuke\Events\WebhookRevokeEvent;
use Nuke\Events\WebhookAuthorizeEvent;
use Nuke\Events\BrowserAuthorizeEvent;
use Nuke\Exceptions\MissingEventPropertyException;

class Event
{
    /**
     * Construct event
     *
     * @param array|null $data
     * @return WebhookAuthorizeEvent|WebhookRevokeEvent|WebhookNukeEvent|BrowserAuthorizeEvent
     * @throws MissingEventPropertyException
     */
    public static function construct(
        ?array $data
    ): WebhookAuthorizeEvent|WebhookRevokeEvent|WebhookNukeEvent|BrowserAuthorizeEvent {
        switch ($data['event']['type'] ?? null) {
            case WebhookAuthorizeEvent::getName():
                $event = (new WebhookAuthorizeEvent());
                $event->token = ($data['event']['data']['token'] ?? null);
                return $event;

            case WebhookRevokeEvent::getName():
                $event = (new WebhookRevokeEvent());
                $event->token = ($data['event']['data']['token'] ?? null);
                return $event;

            case WebhookNukeEvent::getName():
                $event = (new WebhookNukeEvent());
                $event->token = ($data['event']['data']['token'] ?? null);
                return $event;

            case BrowserAuthorizeEvent::getName():
                $event = (new BrowserAuthorizeEvent());
                $event->redirect_uri = ($data['event']['data']['redirect_uri'] ?? null);
                $event->nuke_identifier = ($data['event']['data']['nuke_identifier'] ?? null);
                $event->nuke_token = ($data['event']['data']['nuke_token'] ?? null);
                return $event;

            default:
                throw new MissingEventPropertyException('event.type');
        }
    }
}
