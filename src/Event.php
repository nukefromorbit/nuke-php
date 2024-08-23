<?php

namespace Nuke;

// TODO: Event naming?
// TODO: Event token/state naming.

use Nuke\Events\NukeEvent;
use Nuke\Events\RevokeEvent;
use Nuke\Events\VerificationEvent;
use Nuke\Events\AuthorizeEvent;
use Nuke\Exceptions\MissingEventPropertyException;

class Event
{
    /**
     * Construct event
     *
     * @param array|null $data
     * @return VerificationEvent|RevokeEvent|NukeEvent|AuthorizeEvent
     * @throws MissingEventPropertyException
     */
    public static function construct(?array $data): VerificationEvent|RevokeEvent|NukeEvent|AuthorizeEvent
    {
        switch ($data['event']['type'] ?? null) {
            case VerificationEvent::getName():
                $event = (new VerificationEvent());
                $event->token = ($data['event']['data']['token'] ?? null);
                return $event;

            case RevokeEvent::getName():
                $event = (new RevokeEvent());
                $event->token = ($data['event']['data']['token'] ?? null);
                return $event;

            case NukeEvent::getName():
                $event = (new NukeEvent());
                $event->token = ($data['event']['data']['token'] ?? null);
                $event->actions = ($data['event']['data']['actions'] ?? null);
                return $event;

            case AuthorizeEvent::getName():
                $event = (new AuthorizeEvent());
                $event->redirect_uri = ($data['event']['data']['redirect_uri'] ?? null);
                $event->state = ($data['event']['data']['state'] ?? null);
                $event->nuke_identifier = ($data['event']['data']['nuke_identifier'] ?? null);
                return $event;

            default:
                throw new MissingEventPropertyException('event.type');
        }
    }
}
