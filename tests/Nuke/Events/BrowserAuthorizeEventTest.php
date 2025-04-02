<?php

namespace Nuke\Events;

use Nuke\Crypt;
use Nuke\Nuke;
use Nuke\Event;
use Nuke\Exceptions\InvalidEventPropertyException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(BrowserAuthorizeEvent::class)]
#[UsesClass(Nuke::class)]
#[UsesClass(Event::class)]
#[UsesClass(Crypt::class)]
final class BrowserAuthorizeEventTest extends TestCase
{
    private const NUKE_IDENTIFIER = '6lMyeWiyWZYeA1hBbtrtgGNQXMKbD9fV';
    private const NUKE_SECRET = 'GvFyG1eeaxyjVmFAWhCI6f8DZsYZch1rux3iFyBYgxU=';

    public function testInvalidEventPropertyTypeException(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        $_GET = [];

        $this->expectException(InvalidEventPropertyException::class);
        Event::construct(BrowserAuthorizeEvent::class);
    }

    public function testInvalidEventPropertySourceException(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        /** @noinspection PhpArrayWriteIsNotUsedInspection */
        $_GET = [
            'type' => BrowserAuthorizeEvent::getType(),
        ];

        $this->expectException(InvalidEventPropertyException::class);
        Event::construct(BrowserAuthorizeEvent::class);
    }

    public function testInvalidEventPropertyNukeIdentifierException(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        /** @noinspection PhpArrayWriteIsNotUsedInspection */
        $_GET = [
            'type' => BrowserAuthorizeEvent::getType(),
            'source' => BrowserAuthorizeEvent::SOURCE_NUKE,
        ];

        $this->expectException(InvalidEventPropertyException::class);
        Event::construct(BrowserAuthorizeEvent::class);
    }

    public function testInvalidEventPropertyNukeTokenException(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        /** @noinspection PhpArrayWriteIsNotUsedInspection */
        $_GET = [
            'type' => BrowserAuthorizeEvent::getType(),
            'source' => BrowserAuthorizeEvent::SOURCE_NUKE,
            'nuke_identifier' => Nuke::$identifier,
        ];

        $this->expectException(InvalidEventPropertyException::class);
        Event::construct(BrowserAuthorizeEvent::class);
    }

    public function testInvalidEventPropertyRedirectUriException(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        /** @noinspection PhpArrayWriteIsNotUsedInspection */
        $_GET = [
            'type' => BrowserAuthorizeEvent::getType(),
            'source' => BrowserAuthorizeEvent::SOURCE_NUKE,
            'nuke_identifier' => Nuke::$identifier,
            'nuke_token' => base64_encode(random_bytes(64)),
        ];

        $this->expectException(InvalidEventPropertyException::class);
        Event::construct(BrowserAuthorizeEvent::class);
    }

    public function testConstruct(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        $token = base64_encode(random_bytes(64));
        /** @noinspection PhpArrayWriteIsNotUsedInspection */
        $_GET = [
            'type' => BrowserAuthorizeEvent::getType(),
            'source' => BrowserAuthorizeEvent::SOURCE_NUKE,
            'redirect_uri' => Nuke::APP_URL,
            'nuke_identifier' => Nuke::$identifier,
            'nuke_token' => Nuke::encrypt($token),
        ];

        $event = Event::construct(BrowserAuthorizeEvent::class);
        $this->assertEquals(BrowserAuthorizeEvent::SOURCE_NUKE, $event->source);
        $this->assertEquals(Nuke::$identifier, $event->nuke_identifier);
        $this->assertEquals($token, Nuke::decrypt($event->nuke_token));
        $this->assertEquals(Nuke::APP_URL, $event->redirect_uri);
    }

    public function testGetType(): void
    {
        $this->assertEquals('authorize', BrowserAuthorizeEvent::getType());
    }
}
