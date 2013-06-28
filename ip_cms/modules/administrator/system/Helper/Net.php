<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 *
 */

namespace Modules\administrator\system\Helper;


class Net
{

    public function downloadFile($url, $fileName)
    {
        
        if (!function_exists('curl_init')) {
            throw new UpdateException("CURL is not installed. Please download this file $url and put it in following directory $fileName manually.");
        }
        
        $fs = new FileSystem();

        $destinationDir = $fs->getParentDir($fileName);
        $fs->createWritableDir($destinationDir);
        $fs->makeWritable($destinationDir);

        $ch = curl_init();
        
        $fh = fopen($fileName, 'w'); 
        
        $options = array(
            CURLOPT_FILE => $fh,
            CURLOPT_TIMEOUT => 1800, // set this to 30 min so we don't timeout on big files
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