<?php

namespace Nuke\Events;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(WebhookRevokeEvent::class)]
final class WebhookRevokeEventTest extends TestCase
{
    public function testGetType(): void
    {
        $this->assertEquals('revoke', WebhookRevokeEvent::getType());
    }
}
