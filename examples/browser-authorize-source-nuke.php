<?php

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Nuke\Nuke;
use Nuke\Event;
use Nuke\Events\BrowserAuthorizeEvent;

try {
    // Randomly generated.
    $nukeIdentifier = bin2hex(random_bytes(16));
    $nukeSecret = bin2hex(random_bytes(32));

    // Initialize Nuke Service.
    Nuke::setIdentifier($nukeIdentifier);
    Nuke::setSecret($nukeSecret);

    // Request simulation, this will come from the Nuke App.
    $_GET = [
        'type' => BrowserAuthorizeEvent::getType(),
        'source' => BrowserAuthorizeEvent::SOURCE_NUKE,
        'redirect_uri' => Nuke::APP_URL,
        'nuke_identifier' => Nuke::$identifier,
        'nuke_token' => Nuke::encrypt(bin2hex(random_bytes(64))),
    ];

    // Event construct which will return an BrowserAuthorizeEvent class.
    // This will also validate the request through Nuke::decrypt.
    // Because of that it can throw exceptions.
    $event = Event::construct(BrowserAuthorizeEvent::class);

    // This token will be used to confirm the authorize, revoke access and perform Nuke actions.
    // You will have to internally save this token against the user.
    $token = bin2hex(random_bytes(128));

    // Tip: You can generate a random token and send it as
    // Nuke::encrypt(json_encode(['user_id' => ..., 'token' => $token,])).
    // This will allow you to identify the user faster for the nuke, revoke, etc... events
    // and verify the token against it.

    // Response, this will be a redirect with http_build_query to the redirect_uri.
    $payload = [
        'type' => $event::getType(),
        'source' => $event->source,
        'service_token' => Nuke::encrypt($token),
        'nuke_identifier' => $event->nuke_identifier,
        'nuke_token' => $event->nuke_token,
    ];

    echo 'Redirect: ' . $event->redirect_uri . '?' . http_build_query($payload) . "\n";
} catch (Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
}
