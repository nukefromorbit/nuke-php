<?php

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Nuke\Nuke;
use Nuke\Event;
use Nuke\Events\WebhookAuthorizeEvent;

try {
    // Randomly generated.
    $nukeIdentifier = bin2hex(random_bytes(16));
    $nukeSecret = bin2hex(random_bytes(32));

    // Initialize Nuke Service.
    Nuke::setIdentifier($nukeIdentifier);
    Nuke::setSecret($nukeSecret);

    // Request simulation, this will come from the Nuke API.
    $_POST = [
        'event' => [
            'type' => WebhookAuthorizeEvent::getType(),
            'data' => [
                'token' => bin2hex(random_bytes(128)),
            ],
        ],
    ];
    $_SERVER = [
        'HTTP_' . Nuke::HEADER_X_NUKE_IDENTIFIER => Nuke::$identifier,
        'HTTP_' . Nuke::HEADER_X_NUKE_SIGNATURE => Nuke::getSignature(time(), $_POST),
    ];

    // Event construct which will return a WebhookAuthorizeEvent class.
    // This will also validate the request through Nuke::verifyIdentifierAndSignature.
    // Because of that it can throw exceptions.
    $event = Event::construct(WebhookAuthorizeEvent::class);

    // Implement your authorize actions.
    // The $event->token is the token you saved against your user id.

    // Response.
    echo 'Headers: ' . json_encode(['Status-Code' => '204 No Content',], JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
}
