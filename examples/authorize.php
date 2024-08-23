<?php

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Nuke\Nuke;
use Nuke\Event;
use Nuke\Events\BrowserAuthorizeEvent;

// Initialize Nuke Service
$nukeIdentifier = 'da0119a2(...)75f45571';
$nukeSecret = 'f73c546f(..)6fa03bff';

Nuke::setIdentifier($nukeIdentifier);
Nuke::setSecret($nukeSecret);

try {
    // Request
    $payload = [
        'type' => BrowserAuthorizeEvent::getName(),
        'redirect_uri' => 'https://app.nuke.app/',
        'state' => Nuke::encrypt(bin2hex(random_bytes(64))),
        'nuke_identifier' => Nuke::$identifier,
    ];

    // Validate Payload State
    Nuke::decrypt($payload['state']);

    // Event Construct
    $event = Event::construct($payload);

    // Response
    $payload = [
        'state' => $event->state,
        'token' => Nuke::encrypt(bin2hex(random_bytes(64))),
        'nuke_identifier' => Nuke::$identifier,
    ];

    echo 'Payload: ' . json_encode($payload) . "\n";
} catch (Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
}
