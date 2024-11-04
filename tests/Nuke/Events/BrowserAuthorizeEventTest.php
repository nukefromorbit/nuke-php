<?php

namespace Nuke\Events;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BrowserAuthorizeEvent::class)]
final class BrowserAuthorizeEventTest extends TestCase
{
    public function testGetType(): void
    {
        $this->assertEquals('authorize', BrowserAuthorizeEvent::getType());
    }
}
