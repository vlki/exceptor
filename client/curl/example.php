<?php

session_start();

try {
    throw new Exception('testing exception', 546);
} catch (Exception $e) {
    require dirname(__FILE__) . '/ExceptorClient/Curl.php';
    $client = new ExceptorClient_Curl('exceptor-server', 'some-key');
    $client->logException($e);
}