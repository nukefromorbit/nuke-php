<?php

namespace Nuke;

use Nuke\Events\WebhookNukeEvent;
use Nuke\Events\WebhookRevokeEvent;
use Nuke\Events\WebhookAuthorizeEvent;
use Nuke\Events\BrowserAuthorizeEvent;
use Nuke\Exceptions\InvalidEventClassException;
use Nuke\Exceptions\InvalidEventPropertyException;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Event::class)]
#[UsesClass(Nuke::class)]
#[UsesClass(Crypt::class)]
#[UsesClass(WebhookNukeEvent::class)]
#[UsesClass(WebhookRevokeEvent::class)]
#[UsesClass(WebhookAuthorizeEvent::class)]
#[UsesClass(BrowserAuthorizeEvent::class)]
final class EventTest extends TestCase
{
    private const NUKE_IDENTIFIER = '85cc991a23025910772a5fc7f42b3387';
    private const NUKE_SECRET = '7df8fc9466a07da2710af3b5514e4403faecc3df54908c5b6c5214158d24cb1e';

    public function testConstructWebhookAuthorizeEventInvalidEventPropertyException(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        $_POST = [];
        $_SERVER = [
            'HTTP_' . Nuke::HEADER_X_NUKE_IDENTIFIER => Nuke::$identifier,
            'HTTP_' . Nuke::HEADER_X_NUKE_SIGNATURE => Nuke::getSignature(time(), $_POST),
        ];

        $this->expectException(InvalidEventPropertyException::class);
        Event::construct(WebhookAuthorizeEvent::class);
    }

    public function testConstructWebhookAuthorizeEvent(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        $token = bin2hex(random_bytes(128));
        $_POST = [
            'event' => [
                'type' => WebhookAuthorizeEvent::getType(),
                'data' => [
                    'token' => $token,
                ],
            ],
        ];
        $_SERVER = [
            'HTTP_' . Nuke::HEADER_X_NUKE_IDENTIFIER => Nuke::$identifier,
            'HTTP_' . Nuke::HEADER_X_NUKE_SIGNATURE => Nuke::getSignature(time(), $_POST),
        ];

        $event = Event::construct(WebhookAuthorizeEvent::class);
        $this->assertEquals($token, $event->token);
    }

    public function testConstructWebhookRevokeEventInvalidEventPropertyException(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        $_POST = [];
        $_SERVER = [
            'HTTP_' . Nuke::HEADER_X_NUKE_IDENTIFIER => Nuke::$identifier,
            'HTTP_' . Nuke::HEADER_X_NUKE_SIGNATURE => Nuke::getSignature(time(), $_POST),
        ];

        $this->expectException(InvalidEventPropertyException::class);
        $event = Event::construct(WebhookRevokeEvent::class);
    }

    public function testConstructWebhookRevokeEvent(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        $token = bin2hex(random_bytes(128));
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

    public function testConstructWebhookNukeEventInvalidEventPropertyException(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        $_POST = [];
        $_SERVER = [
            'HTTP_' . Nuke::HEADER_X_NUKE_IDENTIFIER => Nuke::$identifier,
            'HTTP_' . Nuke::HEADER_X_NUKE_SIGNATURE => Nuke::getSignature(time(), $_POST),
        ];

        $this->expectException(InvalidEventPropertyException::class);
        $event = Event::construct(WebhookNukeEvent::class);
    }

    public function testConstructWebhookNukeEvent(): void
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

    public function testConstructBrowserAuthorizeEvent(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        $token = bin2hex(random_bytes(64));
        /** @noinspection PhpArrayWriteIsNotUsedInspection */
        $_GET = [
            'redirect_uri' => Nuke::APP_URL,
            'nuke_identifier' => Nuke::$identifier,
            'nuke_token' => Nuke::encrypt($token),
        ];

        $event = Event::construct(BrowserAuthorizeEvent::class);
        $this->assertEquals(Nuke::$identifier, $event->nuke_identifier);
        $this->assertEquals($token, Nuke::decrypt($event->nuke_token));
        $this->assertEquals(Nuke::APP_URL, $event->redirect_uri);
    }

    public function testConstructInvalidClassException(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        $this->expectException(InvalidEventClassException::class);
        Event::construct('Invalid');
    }
}
