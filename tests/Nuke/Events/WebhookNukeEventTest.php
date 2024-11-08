<?php

namespace Nuke\Events;

use Nuke\Crypt;
use Nuke\Event;
use Nuke\Nuke;
use Nuke\Exceptions\InvalidEventPropertyException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(WebhookNukeEvent::class)]
#[UsesClass(Nuke::class)]
#[UsesClass(Event::class)]
#[UsesClass(Crypt::class)]
final class WebhookNukeEventTest extends TestCase
{
    private const NUKE_IDENTIFIER = '85cc991a23025910772a5fc7f42b3387';
    private const NUKE_SECRET = '7df8fc9466a07da2710af3b5514e4403faecc3df54908c5b6c5214158d24cb1e';

    public function testInvalidEventPropertyTypeException(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        $_POST = [];
        $_SERVER = [
            'HTTP_' . Nuke::HEADER_X_NUKE_IDENTIFIER => Nuke::$identifier,
            'HTTP_' . Nuke::HEADER_X_NUKE_SIGNATURE => Nuke::getSignature(time(), $_POST),
        ];

        $this->expectException(InvalidEventPropertyException::class);
        Event::construct(WebhookNukeEvent::class);
    }

    public function testConstruct(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        $token = bin2hex(random_bytes(128));
        $_POST = [
            'event' => [
                'type' => WebhookNukeEvent::getType(),
                'data' => [
                    'token' => $token,
                ],
            ],
        ];
        $_SERVER = [
            'HTTP_' . Nuke::HEADER_X_NUKE_IDENTIFIER => Nuke::$identifier,
            'HTTP_' . Nuke::HEADER_X_NUKE_SIGNATURE => Nuke::getSignature(time(), $_POST),
        ];

        $event = Event::construct(WebhookNukeEvent::class);
        $this->assertEquals($token, $event->token);
    }

    public function testGetType(): void
    {
        $this->assertEquals('nuke', WebhookNukeEvent::getType());
    }
}
