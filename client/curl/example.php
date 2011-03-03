<?php

session_start();

try {
    throw new Exception('testing exception', 546);
} catch (Exception $e) {
    require dirname(__FILE__) . '/ExceptorClientCurl.php';
    $client = new ExceptorClientCurl('exceptor-server', 'some-key');
    $client->logException($e);
}