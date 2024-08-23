<?php

namespace Nuke;

// TODO: Event naming?
// TODO: Event token/state naming.

use Nuke\Events\NukeEvent;
use Nuke\Events\RevokeEvent;
use Nuke\Events\AuthorizeEvent;
use Nuke\Events\VerificationEvent;
use Nuke\Exceptions\MissingEventPropertyException;

class Event
{
    /**
     * Construct event
     *
     * @param array|null $data
     * @return VerificationEvent|AuthorizeEvent|RevokeEvent|NukeEvent
     * @throws MissingEventPropertyException
     */
    public static function construct(?array $data): VerificationEvent|AuthorizeEvent|RevokeEvent|NukeEvent
    {
        switch ($data['event']['type'] ?? null) {
            case VerificationEvent::getName():
                $event = (new VerificationEvent());
                $event->token = ($data['event']['data']['token'] ?? null);
                return $event;

            case AuthorizeEvent::getName():
                $event = (new AuthorizeEvent());
                $event->state = ($data['event']['data']['state'] ?? null);
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

            default:
                throw new MissingEventPropertyException('event.type');
        }
    }
}
