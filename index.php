<?php
include __DIR__ . '/model/amoCrmAdapter.php';

$adapter = new amoCrmAdapter('subdomain');
//$auth = $adapter->autorizationFromGuzzleHttp('username', 'hash');
try {
    $auth = $adapter->autorization('username', 'hash');

    $contact = $adapter->Query('contact', '89101102737');
    printf($contact);
} catch (\AmoCRM\Exception $e) {
    printf($e->getMessage());
}

print_r($contact);