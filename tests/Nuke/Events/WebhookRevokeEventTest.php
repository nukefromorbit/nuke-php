<?php

namespace Nuke\Events;

use Nuke\Crypt;
use Nuke\Event;
use Nuke\Nuke;
use Nuke\Exceptions\InvalidEventPropertyException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(WebhookRevokeEvent::class)]
#[UsesClass(Nuke::class)]
#[UsesClass(Event::class)]
#[UsesClass(Crypt::class)]
final class WebhookRevokeEventTest extends TestCase
{
    private const NUKE_IDENTIFIER = '6lMyeWiyWZYeA1hBbtrtgGNQXMKbD9fV';
    private const NUKE_SECRET = 'GvFyG1eeaxyjVmFAWhCI6f8DZsYZch1rux3iFyBYgxU=';

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
        Event::construct(WebhookRevokeEvent::class);
    }

    public function testInvalidEventPropertyTokenException(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        $_POST = [
            'event' => ['type' => WebhookRevokeEvent::getType(),],
        ];
        $_SERVER = [
            'HTTP_' . Nuke::HEADER_X_NUKE_IDENTIFIER => Nuke::$identifier,
            'HTTP_' . Nuke::HEADER_X_NUKE_SIGNATURE => Nuke::getSignature(time(), $_POST),
        ];

        $this->expectException(InvalidEventPropertyException::class);
        Event::construct(WebhookRevokeEvent::class);
    }

    public function testConstruct(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        $token = base64_encode(random_bytes(128));
        $_POST = [
            'event' => [
                'type' => WebhookRevokeEvent::getType(),
                'data' => [
                    'token' => $token,
                ],
            ],
        ];
        $_SERVER = [
            'HTTP_' . Nuke::HEADER_X_NUKE_IDENTIFIER => Nuke::$identifier,
            'HTTP_' . Nuke::HEADER_X_NUKE_SIGNATURE => Nuke::getSignature(time(), $_POST),
        ];

        $event = Event::construct(WebhookRevokeEvent::class);
        $this->assertEquals($token, $event->token);
    }

    public function testGetType(): void
    {
        $this->assertEquals('revoke', WebhookRevokeEvent::getType());
    }
}
