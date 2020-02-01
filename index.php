<?php
include __DIR__ . '/model/amoCrmAdapter.php';


$adapter = new amoCrmAdapter('subdomain','login', 'hash');
$auth = $adapter->autorization();
$contact = $adapter->Query('contact','Василий Петрович');
$lead = $adapter->Query('lead','test');
$company = $adapter->Query('company','тест');
$note = $adapter->Query('note','test','lead');
