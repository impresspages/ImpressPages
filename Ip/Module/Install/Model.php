<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Module\Install;


class Model
{
    public static function completeStep($step)
    {
        //if($_SESSION['step'] < $step)
        $_SESSION['step'] = $step;
    }

    public static function checkRequirements()
    {
        $error = array();
        $warning = array();

        if (function_exists('apache_get_modules')) {
            if (!in_array('mod_rewrite', apache_get_modules()))
                $error['mod_rewrite'] = 1;
        }

        if (function_exists('apache_get_modules')) {
            if (!in_array('mod_rewrite', apache_get_modules()))
                $error['mod_rewrite'] = 1;
        }

        if (!class_exists('PDO')) {
            $error['mod_pdo'] = 1;
        }

        if (!file_exists(\Ip\Config::baseFile('.htaccess'))) {
            $error['htaccess'] = 1;
        }

        if (file_exists(\Ip\Config::baseFile('index.html'))) {
            $error['index.html'] = 1;
        }

        if (!extension_loaded('gd') || !function_exists('gd_info')) {
            $error['gd_lib'] = 1;
        }

        if (get_magic_quotes_gpc()) {
            $warning['magic_quotes'] = 1;
        }


        if (!function_exists('curl_init')) {
            $warning['curl'] = 1;
        }

        // TODOX Algimantas: what's that?
        if (function_exists('curl_init')) {
            if (session_id() == '') { //session hasn't been started
                $warning['session'] = 1;
            }
        }

        $answer = '<h1>' . IP_STEP_CHECK_LONG . "</h1>";

        $table = array();

        $table[] = IP_PHP_VERSION;
        if (isset($error['php_version']))
            $table[] = '<span class="error">' . IP_ERROR . "</span>";
        else
            $table[] = '<span class="correct">' . IP_OK . '</span>';

        $table[] = IP_MOD_REWRITE;
        if (isset($error['mod_rewrite']))
            $table[] = '<span class="error">' . IP_ERROR . "</span>";
        else
            $table[] = '<span class="correct">' . IP_OK . '</span>';


        $table[] = IP_MOD_PDO;
        if (isset($error['mod_pdo']))
            $table[] = '<span class="error">' . IP_ERROR . "</span>";
        else
            $table[] = '<span class="correct">' . IP_OK . '</span>';

        $table[] = IP_GD_LIB;
        if (isset($error['gd_lib']))
            $table[] = '<span class="error">' . IP_ERROR . "</span>";
        else
            $table[] = '<span class="correct">' . IP_OK . '</span>';

//sessions are checked using curl. If there is no curl, session availability hasn't been checked
        if (!isset($warning['curl'])) {
            $table[] = IP_SESSION;
            if (isset($warning['session'])) {
                $table[] = '<span class="error">' . IP_ERROR . "</span>";
            } else {
                $table[] = '<span class="correct">' . IP_OK . '</span>';
            }
        }

        $table[] = IP_HTACCESS;
        if (isset($error['htaccess']))
            $table[] = '<span class="error">' . IP_ERROR . "</span>";
        else
            $table[] = '<span class="correct">' . IP_OK . '</span>';


        $table[] = IP_INDEX_HTML;
        if (isset($error['index.html']))
            $table[] = '<span class="error">' . IP_ERROR . "</span>";
        else
            $table[] = '<span class="correct">' . IP_OK . '</span>';


        $table[] = IP_MAGIC_QUOTES;
        if (isset($warning['magic_quotes']))
            $table[] = '<span class="error">' . IP_ERROR . "</span>";
        else
            $table[] = '<span class="correct">' . IP_OK . '</span>';

        $table[] = IP_CURL;
        if (isset($warning['curl'])) {
            $table[] = '<span class="warning">' . IP_WARNING . "</span>";
        } else {
            $table[] = '<span class="correct">' . IP_OK . '</span>';
        }

        $table[] = str_replace('[[memory_limit]]', ini_get('memory_limit'), IP_MEMORY_LIMIT);
        if ((integer)ini_get('memory_limit') < 100) {
            $table[] = '<span class="warning">' . IP_WARNING . "</span>";
        } else {
            $table[] = '<span class="correct">' . IP_OK . "</span>";
        }


        $table[] = '';
        $table[] = '';


        $table[] = '';
        $table[] = '';


        $table[] = '<b>/file/</b> ' . IP_WRITABLE . ' ' . IP_SUBDIRECTORIES;

        if (!Helper::isDirectoryWritable(\Ip\Config::fileDirFile(''))) {
            $table[] = '<span class="error">' . IP_ERROR . "</span>";
            $error['writable_file'] = 1;
        } else
            $table[] = '<span class="correct">' . IP_OK . '</span>';


        $table[] = '<b>/ip_themes/</b> ' . IP_WRITABLE;
        if (!Helper::isDirectoryWritable(dirname(\Ip\Config::themeFile('')))) {
            $table[] = '<span class="error">' . IP_ERROR . "</span>";
            $error['writable_themes'] = 1;
        } else
            $table[] = '<span class="correct">' . IP_OK . '</span>';


        $table[] = '<b>/ip_config.php</b> ' . IP_WRITABLE;

        if (!is_writable(\Ip\Config::baseFile('ip_config.php'))) {
            $table[] = '<span class="error">' . IP_ERROR . "</span>";
            $error['writable_config'] = 1;
        } else
            $table[] = '<span class="correct">' . IP_OK . '</span>';


        $table[] = '<b>/robots.txt</b> ' . IP_WRITABLE;
        if (!is_writable(\Ip\Config::baseFile('robots.txt'))) {
            $table[] = '<span class="error">' . IP_ERROR . "</span>";
            $error['writable_robots'] = 1;
        } else
            $table[] = '<span class="correct">' . IP_OK . '</span>';


        $answer .= Helper::gen_table($table);

        $answer .= '<br><br>';
        if (sizeof($error) > 0) {
            $_SESSION['step'] = 1;
            $answer .= '<a class="button_act" href="?step=1">' . IP_CHECK_AGAIN . '</a>';
        } else {
            Model::completeStep(1);
            $answer .= '<a class="button_act" href="?step=2">' . IP_NEXT . '</a><a class="button" href="?step=1">' . IP_CHECK_AGAIN . '</a>';
        }
        $answer .= "<br>";

        return $answer;
    }
}