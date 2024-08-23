<?php

require 'vendor/autoload.php';

use Nuke\Nuke;
use Nuke\Event;

$nukeIdentifier = 'da0119a2(...)75f45571';
$nukeSecret = 'f73c546f(..)6fa03bff';

Nuke::setIdentifier($nukeIdentifier);
Nuke::setSecret($nukeSecret);

// Request
$headers = [
    'Nuke-Identifier' => 'da0119a2(...)75f45571',
    'Nuke-Signature' => 't=1724415508, v=9490a2023ccaced0803bf00392f6fef0f890037ce935cb2c8bfd30a8550c04b9',
];
$payload = [
    'event' => [
        'type' => 'verification',
        'data' => [
            'token' => '8d714862(...)1c6a019d'
        ],
    ]
];

try {
    // Validate request
    Nuke::verifyIdentifierAndSignature(
        $headers['Nuke-Identifier'],
        $headers['Nuke-Signature'],
        $payload
    );

    // Event construct
    $event = Event::construct($payload);

    // Response
    $headers = [
        'Nuke-Identifier' => 'da0119a2(...)75f45571',
        'Nuke-Signature' => 't=1724415508, v=9490a2023ccaced0803bf00392f6fef0f890037ce935cb2c8bfd30a8550c04b9',
    ];
    $payload = [
        'event' => [
            'type' => 'verification',
            'data' => [
                'token' => strrev($event->token)
            ],
        ]
    ];

    echo 'Headers: ' . json_encode($headers) . "\n";
    echo 'Payload: ' . json_encode($payload) . "\n";
} catch (Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
}
