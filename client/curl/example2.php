<?php

class Foo
{
    public function callFoo(Bar $bar)
    {
        $bar->callBar(array('some param', 15));
    }
}

class Bar
{
    public function callBar($arrayParam)
    {
        throw new Exception('testing exception', 546);
    }
}

$bar = new Bar();
$foo = new Foo();

try {
    $foo->callFoo($bar);
} catch (Exception $e) {
    require dirname(__FILE__) . '/ExceptorClient/Curl.php';
    $client = new ExceptorClient_Curl('exceptor-server', 'some-key');
    $client->logException($e);
}