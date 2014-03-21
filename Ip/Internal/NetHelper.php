<?php
/**
 * @package ImpressPages
 */

namespace Ip\Internal;


class NetHelper
{

    /**
     * @var string
     */
    protected $lastError = null;

    public function __construct()
    {

    }

    /**
     * @return string
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    public function downloadFile($url, $destinationDir, $desiredFilename, $forceFilename = false)
    {
        if (!$forceFilename) {
            $desiredFilename = \Ip\Internal\File\Functions::genUnoccupiedName($desiredFilename, $destinationDir);
        }

        if (!function_exists('curl_init')) {
            throw new \Exception('CURL is not installed. Cannot download file from URL.');
        }

        $ch = curl_init();

        $fh = fopen($destinationDir.$desiredFilename, 'w');

        $options = array(
            CURLOPT_FILE => $fh,
            CURLOPT_TIMEOUT => 1800, // set this to 30 min so we don't timeout on big files
            CURLOPT_URL => $url
        );

        curl_setopt_array($ch, $options);

        if (curl_exec($ch)) {
            return $desiredFilename;
        } else {
            $this->lastError = curl_error($ch);
            return false;
        }
    }

    public function fetchUrl($uri)
    {
        $handle = curl_init();

        curl_setopt($handle, CURLOPT_URL, $uri);
        curl_setopt($handle, CURLOPT_POST, false);
        curl_setopt($handle, CURLOPT_BINARYTRANSFER, false);
        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($handle);
        $headerLength  = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $body     = substr($response, $headerLength);

        // If HTTP response is not 200, throw exception
        if ($httpCode != 200) {
            throw new \Ip\Exception('Could not fetch uri', array('httpCode' => $httpCode));
        }

        return $body;
    }


}
