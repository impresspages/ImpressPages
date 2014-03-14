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

    protected function __construct(){}

    public static function instance()
    {
        return new NetHelper();
    }

    /**
     * @return string
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    public function downloadFile($url, $destinationDir, $desiredFilename)
    {
        $desiredFilename = \Ip\Internal\File\Functions::genUnoccupiedName($desiredFilename, $destinationDir);

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



}
