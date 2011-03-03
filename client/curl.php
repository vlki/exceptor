<?php

session_start();

try {
    throw new Exception('testing exception', 546);
} catch (Exception $e) {
    $structure = array(
        'server' => $_SERVER,
        'session' => $_SESSION,
        'get' => $_GET,
        'post' => $_POST,
        'exception' => array(
            'class' => get_class($e),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'trace' => $e->getTrace(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ),
    );

    $gzippedData = gzcompress(serialize($structure));

    echo '<h1>Gzipped data</h1>';
    echo '<pre>';
    echo $gzippedData;
    echo '</pre>';

    //echo $gzippedData;

    $content = http_build_query(array('data' => $gzippedData));

    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, 'http://exceptor/app_dev.php/input/key');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //execute post
    $result = curl_exec($ch);

    //close connection
    curl_close($ch);

    echo '<h1>Received HTTP response</h1>';
    echo '<pre>';
    print $result;
}