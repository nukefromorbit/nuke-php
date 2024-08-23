<?php

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Nuke\Nuke;
use Nuke\Event;
use Nuke\Events\VerificationEvent;

// Initialize Nuke Service.
$nukeIdentifier = 'da0119a2(...)75f45571';
$nukeSecret = 'f73c546f(..)6fa03bff';

Nuke::setIdentifier($nukeIdentifier);
Nuke::setSecret($nukeSecret);

try {
    // Request Simulation, this will come from the Nuke API.
    $payload = [
        'event' => [
            'type' => VerificationEvent::getName(),
            'data' => [
                'token' => bin2hex(random_bytes(128 / 2)),
            ],
        ],
    ];
    $headers = [
        Nuke::HEADER_X_NUKE_IDENTIFIER => Nuke::$identifier,
        Nuke::HEADER_X_NUKE_SIGNATURE => Nuke::getSignature(time(), $payload),
    ];

    // Validate Request to make sure it's from the Nuke API.
    Nuke::verifyIdentifierAndSignature(
        $headers[Nuke::HEADER_X_NUKE_IDENTIFIER],
        $headers[Nuke::HEADER_X_NUKE_SIGNATURE],
        $payload
    );

    // Event Construct which will return a VerificationEvent class.
    $event = Event::construct($payload);

    // Response, this will be a response to the webhook call.
    $payload = [
        'event' => [
            'type' => VerificationEvent::getName(),
            'data' => [
                'token' => strrev($event->token),
            ],
        ],
    ];
    $headers = [
        Nuke::HEADER_X_NUKE_IDENTIFIER => Nuke::$identifier,
        Nuke::HEADER_X_NUKE_SIGNATURE => Nuke::getSignature(time(), $payload),
    ];

    echo 'Headers: ' . json_encode($headers, JSON_PRETTY_PRINT) . "\n";
    echo 'Payload: ' . json_encode($payload, JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
}
