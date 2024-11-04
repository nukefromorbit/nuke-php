<?php

namespace Nuke\Events;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(WebhookAuthorizeEvent::class)]
final class WebhookAuthorizeEventTest extends TestCase
{
    public function testGetType(): void
    {
        $this->assertEquals('authorize', WebhookAuthorizeEvent::getType());
    }
}
