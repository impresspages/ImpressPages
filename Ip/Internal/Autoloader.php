<?php

/**
 * @package ImpressPages
 *
 *
 */
namespace Ip;


/**
 * Autoloader class
 */

class Autoloader
{
    public function load($name)
    {

        $fileName = str_replace('\\', '/', $name) . '.php';
        if ($fileName[0] == '/') { //in some environments required class starts with slash. In that case remove the slash.
            $fileName = substr($fileName, 1);
        }

        $possibleFilename = ipFile($fileName);

        if (file_exists($possibleFilename)) {
            require_once $possibleFilename;
            return true;
        }

        $vendorFile = ipFile('Ip/Internal/Vendor/' . $fileName);
        if (file_exists($vendorFile)) {
            require_once $vendorFile;
            return true;
        }

        return false;

    }


}



