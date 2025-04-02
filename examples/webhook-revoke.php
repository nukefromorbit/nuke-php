<?php

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Nuke\Nuke;
use Nuke\Event;
use Nuke\Events\WebhookRevokeEvent;

try {
    // Randomly generated.
    $nukeIdentifier = base64_encode(random_bytes(16));
    $nukeSecret = base64_encode(random_bytes(32));

    // Initialize Nuke Service.
    Nuke::setIdentifier($nukeIdentifier);
    Nuke::setSecret($nukeSecret);

    // Request simulation, this will come from the Nuke API.
    $_POST = [
        'event' => [
            'type' => WebhookRevokeEvent::getType(),
            'data' => [
                'token' => base64_encode(random_bytes(128)),
            ],
        ],
    ];
    $_SERVER = [
        'HTTP_' . Nuke::HEADER_X_NUKE_IDENTIFIER => Nuke::$identifier,
        'HTTP_' . Nuke::HEADER_X_NUKE_SIGNATURE => Nuke::getSignature(time(), $_POST),
    ];

    // Event construct which will return a WebhookRevokeEvent class.
    // This will also validate the request through Nuke::verifyIdentifierAndSignature.
    // Because of that it can throw exceptions.
    $event = Event::construct(WebhookRevokeEvent::class);

    // Implement your revoke actions.
    // The $event->token is the token you saved against your user id.

    // Response.
    echo 'Headers: ' . json_encode(['Status-Code' => '204 No Content',], JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
}
