<?php

namespace Modules\standard\design;


/**
 * Class ThemeDownloader
 * @package Modules\standard\design
 *
 * Downloads and extracts theme into themes directory.
 */
class ThemeDownloader
{
    public function downloadTheme($name, $url)
    {
        $net = \Library\Php\Net::instance();
        $themeTempFilename = $net->downloadFile($url, BASE_DIR . TMP_FILE_DIR, $name . '.zip');

        if (!$themeTempFilename) {
            throw new \Ip\CoreException('Theme download failed.');
        }

        $this->extractZip(BASE_DIR . TMP_FILE_DIR . $themeTempFilename, BASE_DIR . THEME_DIR);

        unlink(BASE_DIR . TMP_FILE_DIR . $themeTempFilename);
    }

    private function extractZip($archivePath, $destinationDir)
    {
        if (class_exists('\\ZipArchive')) {
            $zip = new \ZipArchive();
            if ($zip->open($archivePath) === TRUE) {
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