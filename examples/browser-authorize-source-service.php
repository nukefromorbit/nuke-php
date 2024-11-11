<?php

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Nuke\Nuke;
use Nuke\Events\BrowserAuthorizeEvent;

try {
    // Randomly generated.
    $nukeIdentifier = bin2hex(random_bytes(16));
    $nukeSecret = bin2hex(random_bytes(32));

    // Initialize Nuke Service.
    Nuke::setIdentifier($nukeIdentifier);
    Nuke::setSecret($nukeSecret);

    // This token will be used to confirm the authorize, revoke access and perform Nuke actions.
    // You will have to internally save this token against the user.
    $token = bin2hex(random_bytes(128));

    // Tip: You can generate a random token and send it as
    // Nuke::encrypt(json_encode(['user_id' => ..., 'token' => $token,])).
    // This will allow you to identify the user faster for the nuke, revoke, etc... events
    // and verify the token against it.

    // Request simulation, this will come from your service.
    $payload = [
        'type' => BrowserAuthorizeEvent::getType(),
        'source' => BrowserAuthorizeEvent::SOURCE_SERVICE,
        'redirect_uri' => 'https://your-service.tld',
        'nuke_identifier' => Nuke::$identifier,
        'service_token' => Nuke::encrypt($token),
    ];

    echo 'Redirect: ' . Nuke::APP_AUTHORIZE_URL . '?' . http_build_query($payload) . "\n";
} catch (Exception $e) {
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
}
