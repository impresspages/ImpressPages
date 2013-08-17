<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\standard\design;


use \Modules\developer\form as Form;

class Helper
{

    protected function __construct()
    {

    }

    /**
     * @return Helper
     */
    public static function instance()
    {
        return new Helper();
    }


    public function getFirstDir($path)
    {
        $files = scandir($path);
        if (!$files) {
            return false;
        }
        foreach($files as $file) {
            if ($file != '.' && $file != '..' && is_dir($path . '/' . $file)) {
                return $file;
            }
        }
    }

    public function extractZip($archivePath, $destinationDir)
    {
        if (class_exists('\\ZipArchive')) {
            $zip = new \ZipArchive();
            if ($zip->open($archivePath) === true) {
                $zip->extractTo($destinationDir);
                $zip->close();
            } else {
                throw new \Ip\CoreException('Theme extraction failed.');
            }
        } else {
            $zip = new \PclZip($archivePath);
            if (!$zip->extract(PCLZIP_OPT_PATH, $destinationDir)) {
                throw new \Ip\CoreException('Theme extraction failed.');
            }
        }
    }

}