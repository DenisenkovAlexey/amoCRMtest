<?php
include __DIR__ . '/model/amoCrmAdapter.php';

$adapter = new amoCrmAdapter('denisenkovalexey');
$auth = $adapter->autorization('denisenkov.alexey@gmail.com', '813db85697ad922997f26ce27900b44912c01629');
$contact = $adapter->Query('contact','Василий Петрович');
$lead = $adapter->Query('lead','test');
$company = $adapter->Query('company','тест');
$note = $adapter->Query('note','test','lead');
