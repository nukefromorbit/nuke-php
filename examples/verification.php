<?php

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Nuke\Nuke;
use Nuke\Event;
use Nuke\Events\WebhookVerificationEvent;

// Initialize Nuke Service
$nukeIdentifier = 'da0119a2(...)75f45571';
$nukeSecret = 'f73c546f(..)6fa03bff';

Nuke::setIdentifier($nukeIdentifier);
Nuke::setSecret($nukeSecret);

try {
    // Request
    $payload = [
        'event' => [
            'type' => WebhookVerificationEvent::getName(),
            'data' => [
                'token' => bin2hex(random_bytes(128 / 2)),
            ],
        ],
    ];
    $headers = [
        'Nuke-Identifier' => Nuke::$identifier,
        'Nuke-Signature' => Nuke::getSignature(time(), $payload),
    ];

    // Validate Request
    Nuke::verifyIdentifierAndSignature(
        $headers['Nuke-Identifier'],
        $headers['Nuke-Signature'],
        $payload
    );

    // Event Construct
    $event = Event::construct($payload);

    // Response
    $payload = [
        'event' => [
            'type' => WebhookVerificationEvent::getName(),
            'data' => [
                'token' => strrev($event->token),
            ],
        ],
    ];
    $headers = [
        'Nuke-Identifier' => Nuke::$identifier,
        'Nuke-Signature' => Nuke::getSignature(time(), $payload),
    ];

    echo 'Headers: ' . json_encode($headers) . "\n";
    echo 'Payload: ' . json_encode($payload) . "\n";
} catch (Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
}
