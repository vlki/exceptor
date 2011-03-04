<?php

require_once dirname(__FILE__) . '/Curl/Exception.php';

/**
 * Exceptor client built upon cURL.
 *
 * @author Jan Vlcek <vlki@vlki.cz>
 */
class ExceptorClient_Curl
{

    /**
     * Host of the exceptor server app.
     *
     * @var string
     */
    protected $serverHost;

    /**
     * Key of the application used for identification in exceptor server app.
     *
     * @var string
     */
    protected $appKey;

    /**
     * Constructor.
     *
     * @param string $serverHost  Host of exceptor server app (ex. exceptor.example.com)
     * @param string $appKey      Key received from exceptor server app to identify tracked application
     */
    public function __construct($serverHost, $appKey)
    {
        $this->serverHost = $serverHost;
        $this->appKey = $appKey;
    }

    /**
     * Logs exception to exceptor server app.
     *
     * @param Exception $e
     * @return null
     */
    public function logException(Exception $e)
    {
        $session = array();
        if (session_id() !== '') { // sessions don't have to be started
            $session = $_SESSION;
        }

        // build structure
        $structure = array(
            'server' => $_SERVER,
            'session' => $session,
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

        // prepare POST data
        $gzippedData = gzcompress(serialize($structure));
        $content = http_build_query(array('data' => $gzippedData));

        // open connection
        $ch = curl_init();

        // prepare curl
        curl_setopt($ch, CURLOPT_URL, "http://{$this->serverHost}/input/{$this->appKey}");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // execute POST
        $result = curl_exec($ch);

        if (false === $result) {
            $curlError = curl_error($ch);
            curl_close($ch);
            throw new ExceptorClient_Curl_Exception('Logging of exception failed. Curl error: ' . $curlError);
        }

        curl_close($ch);

        if ('Ok' !== $result) {
            throw new ExceptorClient_Curl_Exception("Expected server response should be 'Ok'. Different was returned.");
        }
    }

}