<?php

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Nuke\Nuke;
use Nuke\Event;
use Nuke\Events\WebhookRevokeEvent;

try {
    // Randomly generated.
    $nukeIdentifier = bin2hex(random_bytes(16));
    $nukeSecret = bin2hex(random_bytes(32));

    // Initialize Nuke Service.
    Nuke::setIdentifier($nukeIdentifier);
    Nuke::setSecret($nukeSecret);

    // Request details.
    $_POST = [
        'event' => [
            'type' => WebhookRevokeEvent::getName(),
            'data' => [
                'token' => bin2hex(random_bytes(128)),
            ],
        ],
    ];

    // Request simulation, this will come from the Nuke API.
    $payload = $_POST;
    $headers = [
        Nuke::HEADER_X_NUKE_IDENTIFIER => Nuke::$identifier,
        Nuke::HEADER_X_NUKE_SIGNATURE => Nuke::getSignature(time(), $payload),
    ];

    // Validate request to make sure it's from the Nuke API.
    Nuke::verifyIdentifierAndSignature(
        $headers[Nuke::HEADER_X_NUKE_IDENTIFIER],
        $headers[Nuke::HEADER_X_NUKE_SIGNATURE],
        $payload
    );

    // Event construct which will return a WebhookRevokeEvent class.
    $event = Event::construct($payload);

    // Implement your revoke actions.

    // Response.
    echo 'Headers: ' . json_encode(['Status-Code' => '204 No Content',], JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
}
