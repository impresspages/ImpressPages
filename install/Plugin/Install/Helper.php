<?php
/**
 * @package   ImpressPages
 */

namespace Plugin\Install;


class Helper
{
    /**
     * @param string $dir
     * @return bool
     */
    public static function isDirectoryWritable($dir)
    {
        $dir = rtrim($dir, '/\\');

        if (!is_writable($dir)) {
            return false;
        }

        $handle = opendir($dir);
        if (!$handle) {
            return false;
        }

        while (false !== ($file = readdir($handle))) {
            if ($file != ".." && !is_writable($dir . '/' . $file)) {
                closedir($handle);
                return false;
            }
        }

        closedir($handle);

        return true;
    }

    public static function isInstallAvailable()
    {
        if (!is_file("../config.php") || filesize("../config.php") !== false && filesize("../config.php") < 100) {
            return true;
        } else {
            return false;
        }
    }

    public static function generateMenu($curStep)
    {

        $steps = array();
        $steps[] = __('Language selection', 'Install');
        $steps[] = __('System check', 'Install');
        $steps[] = __('License', 'Install');
        $steps[] = __('Database', 'Install');
        $steps[] = __('Configuration', 'Install');
        $steps[] = __('Finish', 'Install');

        $answer = '
    <div class="list-group">
';

        foreach ($steps as $key => $step) {
            $class = "";
            if ($curStep >= $key) {
                $class = "success";
            }
            if ($key == $curStep) {
                $class = "active";
            }
            if ($key <= $curStep) {
                $answer .= '<a href="index.php?step=' . ($key) . '" class="list-group-item ' . $class . '">' . $step . '</a>';
            } else {
                $answer .= '<a href="#" class="list-group-item ' . $class . '">' . $step . '</a>';
            }

        }

        $answer .= '
    </div>
';

        return $answer;
    }



    public static function randString ( $length ) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = '';
        $size = strlen( $chars );
        for( $i = 0; $i < $length; $i++ ) {
            $str .= $chars[ rand( 0, $size - 1 ) ];
        }

        return $str;
    }
}
