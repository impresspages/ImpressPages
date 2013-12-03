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
        if (filesize("../ip_config.php") !== false && filesize("../ip_config.php") < 100) {
            return true;
        } else {
            return false;
        }
    }

    public static function gen_menu()
    {

        $steps = array();
        $steps[] = __('Language selection', 'ipInstall');
        $steps[] = __('System check', 'ipInstall');
        $steps[] = __('License', 'ipInstall');
        $steps[] = __('Database', 'ipInstall');
        $steps[] = __('Configuration', 'ipInstall');
        $steps[] = __('Finish', 'ipInstall');

        $answer = '
    <ul>
';

        foreach ($steps as $key => $step) {
            $class = "";
            if ($_SESSION['step'] >= $key)
                $class = "completed";
            else {
                $class = "incompleted";
            }
            if ($key == $_SESSION['step']) {
                $class = "current";
            }
            if ($key <= $_SESSION['step']) {
                $answer .= '<li onclick="document.location=\'index.php?step=' . ($key) . '\'" class="' . $class . '"><a href="index.php?step=' . ($key) . '">' . $step . '</a></li>';
            } else {
                $answer .= '<li class="' . $class . '"><a>' . $step . '</a></li>';
            }

        }

        $answer .= '
    </ul>
';

        return $answer;
    }

    public static function gen_table($table)
    {
        $answer = '';

        $answer .= '<table>';
        $i = 0;
        while (sizeof($table) > ($i + 1)) {
            $answer .= '<tr><td class="label">' . $table[$i] . '</td><td class="value">' . $table[$i + 1] . '</td></tr>';
            $i += 2;
        }

        $answer .= '</table>';
        return $answer;
    }
}