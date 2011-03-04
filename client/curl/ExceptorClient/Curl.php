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
                'trace' => $this->getTraces($e),
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
    
    /**
     * Returns an array of exception traces.
     *
     * @param Exception $exception  An Exception implementation instance
     * @param string    $format     The trace format (txt or html)
     *
     * @return array An array of traces
     */
    protected function getTraces($exception, $format = 'txt')
    {
        $traceData = $exception->getTrace();
        array_unshift($traceData, array(
            'function' => '',
            'file'     => $exception->getFile() != null ? $exception->getFile() : null,
            'line'     => $exception->getLine() != null ? $exception->getLine() : null,
            'args'     => array(),
        ));

        $traces = array();
        $lineFormat = 'at %s%s%s(%s) in %s line %s';

        for ($i = 0, $count = count($traceData); $i < $count; $i++)
        {
            $line = isset($traceData[$i]['line']) ? $traceData[$i]['line'] : null;
            $file = isset($traceData[$i]['file']) ? $traceData[$i]['file'] : null;
            $args = isset($traceData[$i]['args']) ? $traceData[$i]['args'] : array();
            $traces[] = sprintf($lineFormat,
                (isset($traceData[$i]['class']) ? $traceData[$i]['class'] : ''),
                (isset($traceData[$i]['type']) ? $traceData[$i]['type'] : ''),
                $traceData[$i]['function'],
                $this->formatArgs($args, false, $format),
                $this->formatFile($file, $line, $format, null === $file ? 'n/a' : $file),
                null === $line ? 'n/a' : $line,
                'trace_'.$i,
                'trace_'.$i,
                $i == 0 ? 'block' : 'none',
                "obsah souboru"
            );
        }
        
        return $traces;
  }
  
    /**
     * Formats an array as a string.
     *
     * @param array   $args     The argument array
     * @param boolean $single
     * @param string  $format   The format string (html or txt)
     *
     * @return string
     */
    protected function formatArgs($args, $single = false)
    {
        $result = array();

        $single and $args = array($args);

        foreach ($args as $key => $value) {
            if (is_object($value)) {
                $formattedValue = 'object'.sprintf("('%s')", get_class($value));
            } else if (is_array($value)) {
                $formattedValue = 'array'.sprintf("(%s)", self::formatArgs($value));
            } else if (is_string($value)) {
                $formattedValue = "'$value'";
            } else if (null === $value) {
                $formattedValue = 'null';
            } else {
                $formattedValue = $value;
            }

            $result[] = is_int($key) ? $formattedValue : sprintf("'%s' => %s", $key, $formattedValue);
        }

        return implode(', ', $result);
    }
    
    /**
     * Formats a file path.
     * 
     * @param  string  $file   An absolute file path
     * @param  integer $line   The line number
     * @param  string  $format The output format (txt or html)
     * @param  string  $text   Use this text for the link rather than the file path
     * 
     * @return string
     */
    protected function formatFile($file, $line, $format = 'html', $text = null)
    {
        if (null === $text) {
            $text = $file;
        }

        if (
            'html' == $format && 
            $file && 
            $line && 
            $linkFormat = ini_get('xdebug.file_link_format')
        ) {
            $link = strtr($linkFormat, array('%f' => $file, '%l' => $line));
            $text = sprintf('<a href="%s" title="Click to open this file" class="file_link">%s</a>', $link, $text);
        }

        return $text;
    }
}
