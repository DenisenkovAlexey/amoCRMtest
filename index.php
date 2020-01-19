<?php
include __DIR__ . '/model/amoCrmAdapter.php';

$adapter = new amoCrmAdapter('subdomain');
$auth = $adapter->autorization('login', 'hash');
$contact = $adapter->Query('contact','Василий Петрович');
$lead = $adapter->Query('lead','test');
$company = $adapter->Query('company','тест');
$note = $adapter->Query('note','test','lead');
