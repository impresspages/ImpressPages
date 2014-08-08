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

        $fh = fopen($destinationDir . $desiredFilename, 'w');

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

        $response = self::curl_exec_follow($handle);
        $headerLength = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $body = substr($response, $headerLength);

        // If HTTP response is not 200, throw exception
        if ($httpCode != 200) {
            throw new \Ip\Exception('Could not fetch uri', array('httpCode' => $httpCode));
        }

        return $body;
    }

    /**
     * @see http://slopjong.de/2012/03/31/curl-follow-locations-with-safe_mode-enabled-or-open_basedir-set/
     *
     * @param $ch
     * @param null $maxredirect
     * @return bool|mixed
     */
    private function curl_exec_follow($ch, &$maxredirect = null)
    {

        $mr = $maxredirect === null ? 5 : intval($maxredirect);

        if (ini_get('open_basedir') == '' && ini_get('safe_mode') == 'Off') {

            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $mr > 0);
            curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        } else {

            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

            if ($mr > 0) {
                $original_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                $newurl = $original_url;

                $rch = curl_copy_handle($ch);

                curl_setopt($rch, CURLOPT_HEADER, true);
                curl_setopt($rch, CURLOPT_NOBODY, true);
                curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
                do {
                    curl_setopt($rch, CURLOPT_URL, $newurl);
                    $header = curl_exec($rch);
                    if (curl_errno($rch)) {
                        $code = 0;
                    } else {
                        $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                        if ($code == 301 || $code == 302) {
                            preg_match('/Location:(.*?)\n/', $header, $matches);
                            $newurl = trim(array_pop($matches));

                            // if no scheme is present then the new url is a
                            // relative path and thus needs some extra care
                            if (!preg_match("/^https?:/i", $newurl)) {
                                $newurl = $original_url . $newurl;
                            }
                        } else {
                            $code = 0;
                        }
                    }
                } while ($code && --$mr);

                curl_close($rch);

                if (!$mr) {
                    if ($maxredirect === null) {
                        trigger_error('Too many redirects.', E_USER_WARNING);
                    } else {
                        $maxredirect = 0;
                    }

                    return false;
                }
                curl_setopt($ch, CURLOPT_URL, $newurl);
            }
        }
        return curl_exec($ch);
    }


}
