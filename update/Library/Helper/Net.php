<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace IpUpdate\Library\Helper;


class Net
{

    public function downloadFile($url, $fileName)
    {
        
        if (!function_exists('curl_init')) {
            throw new \IpUpdate\Library\UpdateException("Can't get download URL", \IpUpdate\Library\UpdateException::CURL_REQUIRED);
        }
        
        $fs = new \IpUpdate\Library\Helper\FileSystem();
        
        $fs->makeWritable($fs->getParentDir($fileName));

        $ch = curl_init();
        
        $fh = fopen($fileName, 'w'); 
        
        $options = array(
            CURLOPT_FILE => $fh,
            CURLOPT_TIMEOUT => 1800, // set this to 30 min so we dont timeout on big files
            CURLOPT_URL => $url
        );
        
        curl_setopt_array($ch, $options);

        if (curl_exec($ch)) {
            return true;
        } else {
            return curl_error($ch);
        }
    }
    
    
}