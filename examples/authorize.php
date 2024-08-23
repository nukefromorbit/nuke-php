<?php

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Nuke\Nuke;
use Nuke\Event;
use Nuke\Events\AuthorizeEvent;

// Initialize Nuke Service.
$nukeIdentifier = 'da0119a2(...)75f45571';
$nukeSecret = 'f73c546f(..)6fa03bff';

Nuke::setIdentifier($nukeIdentifier);
Nuke::setSecret($nukeSecret);

try {
    // Request Simulation, this will come from the Nuke App.
    // This will be a normal browser request, you will have to build the payload from the GET query parameters.
    $payload = [
        'event' => [
            'type' => AuthorizeEvent::getName(),
            'data' => [
                'redirect_uri' => 'https://app.nuke.app/',
                'state' => Nuke::encrypt(bin2hex(random_bytes(64))),
                'nuke_identifier' => Nuke::$identifier,
            ],
        ],
    ];

    // Event Construct which will return an AuthorizeEvent class.
    $event = Event::construct($payload);

    // Validate Event State to make sure it's encrypted with the known key.
    Nuke::decrypt($event->state);

    // This token will be used to confirm the authorization, revoke access and perform Nuke actions.
    // You will have to internally save this token against the user.
    $token = bin2hex(random_bytes(64));

    // Response, this will be a redirect with http_build_query to the redirect_uri.
    $payload = [
        'state' => $event->state,
        'token' => Nuke::encrypt($token),
        'nuke_identifier' => Nuke::$identifier,
    ];

    echo 'Payload: ' . json_encode($payload, JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
}
