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
        if (filesize("../config.php") !== false && filesize("../config.php") < 100) {
            return true;
        } else {
            return false;
        }
    }

    public static function generateMenu()
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
            if ($_SESSION['step'] >= $key) {
                $class = "success";
            }
            if ($key == $_SESSION['step']) {
                $class = "active";
            }
            if ($key <= $_SESSION['step']) {
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

    public static function generateTable($table)
    {
        $answer = '';

        $answer .= '<table class="table">';
        foreach ($table as $row) {
            $typeLabel = $class = '';
            switch ($row['type']) {
                case 'success':
                    $typeLabel = __('Ok', 'Install');
                    $class = 'success';
                    break;
                case 'warning':
                    $typeLabel = __('Warning', 'Install');
                    $class = 'warning';
                    break;
                case 'error':
                    $typeLabel = __('Error', 'Install');
                    $class = 'danger';
                    break;
            }
            $answer .= '<tr><th>' . $row['name'] . '</th><td class="text-center ' . $class . '">' . $typeLabel . '</td></tr>';
        }

        $answer .= '</table>';
        return $answer;
    }
}
