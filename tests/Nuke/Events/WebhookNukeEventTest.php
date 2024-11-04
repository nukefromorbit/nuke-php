<?php

namespace Nuke\Events;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(WebhookNukeEvent::class)]
final class WebhookNukeEventTest extends TestCase
{
    public function testGetType(): void
    {
        $this->assertEquals('nuke', WebhookNukeEvent::getType());
    }
}
