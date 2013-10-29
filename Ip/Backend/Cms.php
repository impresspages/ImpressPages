<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Backend;

use Ip\Backend\Db;
use Ip\Backend\HtmlOutput;
use Ip\Backend\Session;
use Ip\Backend\Template;

/**
 * ImpressPages main administration area class
 *
 * Manages session, logins, rights, loads managemenet tools
 *
 * @package ImpressPages
 */
class Cms {
    var $module;  //current module object
    var $session;
    var $html; //html output object
    var $tinyMce; //true if tinyMce engine is loaded
    var $curModId;
    var $loginError;

    function __construct() {
        $this->session = new Session();


        $this->html = new HtmlOutput();
        $this->module = null;

        $this->tinyMce = false;
        $this->curModId = null;

        $this->loginError = null;

    }



    /**
     * Output management tools
     *
     * @access public
     * @return string Option
     */

    function manage() {
        global $parametersMod;

        //log off
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == "logout" && !isset($_REQUEST['module_id']) && !(isset($_REQUEST['admin_module_group']) && isset($_REQUEST['admin_module_group']))) {
            $this->session->logout();
            $this->html->headerModules();
            $this->html->html('<script type="text/javascript">window.top.location=\'admin.php\';</script>');
            $this->deleteTmpFiles();
            $this->html->footer();
            $this->html->send();
            \db::disconnect();
            exit;
        }
        //eof log off



        //log in
        if(isset($_REQUEST['action']) && isset($_POST['f_name']) && isset($_POST['f_pass']) && $_REQUEST['action'] == "login" && !isset($_REQUEST['module_id'])) {

            if(\Ip\Backend\Db::incorrectLoginCount($_POST['f_name'].'('.$_SERVER['REMOTE_ADDR'].')') > 2) {
                $this->loginError = $parametersMod->getValue('standard', 'configuration', 'system_translations', 'login_suspended');
                \Ip\Backend\Db::log('system', 'backend login suspended', $_POST['f_name'].'('.$_SERVER['REMOTE_ADDR'].')', 2);
            }else {
                $id = \Ip\Backend\Db::userId($_POST['f_name'], $_POST['f_pass']);
                if($id !== false) {
                    $this->session->login($id);
                    \Ip\Backend\Db::log('system', 'backend login', $_POST['f_name'].' ('.$_SERVER['REMOTE_ADDR'].')', 0);
                    header("location:ip_backend_frames.php");
                } else {
                    $this->loginError = $parametersMod->getValue('standard', 'configuration', 'system_translations', 'login_incorrect');
                    \Ip\Backend\Db::log('system', 'backend login incorrect', $_POST['f_name'].'('.$_SERVER['REMOTE_ADDR'].')', 1);
                }
            }
        }
        //eof log in
        if($this->session->loggedIn()) {  //login check
            if (isset($_REQUEST['admin_module_group']) && $_REQUEST['admin_module_name']) {
                $module = \Db::getModule(null, $_REQUEST['admin_module_group'], $_REQUEST['admin_module_name']);
                if ($module) {
                    $_GET['module_id'] = $module['id'];
                    $_REQUEST['module_id'] = $module['id'];
                }
            }
            //create module
            if(isset($_GET['module_id']) && $_GET['module_id'] != '' && \Ip\Backend\Db::allowedModule($_GET['module_id'], $this->session->userId())) {
                /*new module*/
                $newModule = \Db::getModule($_GET['module_id']);
                if ($newModule['core']) {
                    if (!file_exists(MODULE_DIR.$newModule['g_name'].'/'.$newModule['m_name'].'/manager.php') && file_exists(MODULE_DIR.$newModule['g_name'].'/'.$newModule['m_name'].'/AdminController.php')) {
                        header('location: '.BASE_URL.'?g=' . $newModule['g_name'] . '&m=' . $newModule['m_name'] . '&aa=index');
                    } else {
                        require(MODULE_DIR.$newModule['g_name'].'/'.$newModule['m_name'].'/manager.php');
                    }
                } else {
                    // TODOX Plugin dir
                }
                $this->curModId = $newModule['id'];
                eval('$this->module = new \\Modules\\'.$newModule['g_name'].'\\'.$newModule['m_name'].'\\Manager();');
            }else {
                if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'first_module') {
                    /*first module*/
                    $newModule = \Ip\Backend\Db::firstAllowedModule($this->session->userId());
                    if($newModule != false) {
                        $this->curModId = $newModule['id'];
                        if ($newModule['core']) {
                            require(MODULE_DIR.$newModule['g_name'].'/'.$newModule['m_name'].'/manager.php');
                        } else {
                            require(PLUGIN_DIR.$newModule['g_name'].'/'.$newModule['m_name'].'/manager.php');
                        }
                        eval('$this->module = new \\Modules\\'.$newModule['g_name'].'\\'.$newModule['m_name'].'\\Manager();');
                    }
                }elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == 'ping') {
                    $this->html->html('');
                }elseif(!isset($_REQUEST['action'])) {
                    $this->html->html('<html><body><script type="text/javascript">parent.window.top.location=\'ip_backend_frames.php\';</script></body></html>');
                }
            }
            //eof create module


            if ($this->module) {
                $this->html->html($this->module->manage());
            }

        }else {
            if(strpos(BASE_URL, $_SERVER['HTTP_HOST']) != 7 && strpos(BASE_URL, $_SERVER['HTTP_HOST']) != 8 ) {
                /*check if we are in correct subdomain. www.yoursite.com not allways equal to yoursite.com from session perspective)*/
                header("location: ".BASE_URL."admin.php");
                \db::disconnect();
                exit;
            }
            $this->html->html(Template::headerLogin());
            $this->html->html('<script type="text/javascript">if(parent.header && parent.content)parent.window.top.location=\'admin.php\';</script>');
            $this->html->html(Template::loginForm($this->loginError)); //login window
            $this->html->footer();
        }

        $this->html->send();

    }

    function worker() { //make worker actions.
        global $cms;
        global $log;
        global $globalWorker;
        if($this->session->loggedIn()) {  //login check
            //deprecated way
            if (isset($_REQUEST['admin_module_group']) && $_REQUEST['admin_module_name']) {
                $module = \Db::getModule(null, $_GET['admin_module_group'], $_REQUEST['admin_module_name']);
                if ($module) {
                    $_GET['module_id'] = $module['id'];
                    $_REQUEST['module_id'] = $module['id'];
                }
            }
            
            if(isset($_GET['module_id']) && $_GET['module_id'] != '' && \Ip\Backend\Db::allowedModule($_GET['module_id'], $cms->session->userId())) {
                $this->curModId = $_GET['module_id'];
                $newModule = \Db::getModule($_GET['module_id']);

                if(Db::allowedModule($_GET['module_id'], $this->session->userId())){
                    if(file_exists(MODULE_DIR.$newModule['g_name'].'/'.$newModule['m_name'].'/backend_worker.php')) {
                        require_once(MODULE_DIR.$newModule['g_name'].'/'.$newModule['m_name'].'/backend_worker.php');
                    } else {
                        // TODOX Plugin dir
                    }
                    eval('$globalWorker = new \\Modules\\'.$newModule['g_name'].'\\'.$newModule['m_name'].'\\BackendWorker();');
                    $globalWorker->work();
                }
            }
            //eof deprecated way
        }
    }


    function getCurrentUrl() {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= '://';
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }


    function generateUrl($moduleId = null, $getVars = null) { //url to cms module
        if($moduleId == null)
        $moduleId = $this->curModId;
        if($getVars != '')
        return BASE_URL.'?admin=1&module_id='.$moduleId.'&'.$getVars.'&security_token='.$this->session->securityToken();
        else
        return BASE_URL.'?admin=1&module_id='.$moduleId.'&security_token='.$this->session->securityToken();
    }

    function deleteTmpFiles() {
        $dirs = array();
        $dirs[] = \Ip\Config::temporaryFile('');
        $dirs[] = BASE_DIR.TMP_SECURE_DIR;

        foreach($dirs as $key => $dir) {
            $this->cleanDirRecursive($dir);
        }
    }

    private function cleanDirRecursive($dir, $depth = 0)
    {
        if ($depth > 100) {
            return;
        }
        if ($handle = opendir($dir)) {
            $now = time();
            // List all the files
            while (false !== ($file = readdir($handle))) {
                if(file_exists($dir.$file) && $file != ".."  && $file != ".") {
                    if (filectime($dir.$file) + 3600*24*7*2 < $now){  //delete if a file is created more than two weeks ago
                        if (is_dir($dir.$file)) {
                            $this->cleanDirRecursive($dir.$file.'/', $depth + 1);
                            if ($this->dirIsEmpty($dir.$file)) {
                                rmdir($dir.$file);
                            }
                        } else {
                            if ($file != '.htaccess' && ($file != 'readme.txt' || $depth > 0) && ($file != 'readme.md' || $depth > 0)) {
                                unlink($dir.$file);
                            }
                        }
                    }
                }
            }
            closedir($handle);
        }
    }

    private function dirIsEmpty($dir) {
        if (!is_readable($dir)) return NULL;
        return (count(scandir($dir)) == 2);
    }

    public static function usedUrl($url) {

        $systemDirs = array();

        $systemDirs[FILE_DIR] = 1;
        $systemDirs[INCLUDE_DIR] = 1;
        $systemDirs[LIBRARY_DIR] = 1;
        $systemDirs[MODULE_DIR] = 1;
        $systemDirs['.htaccess'] = 1;
        $systemDirs['admin.php'] = 1;
        $systemDirs['ip_backend_frames.php'] = 1;
        $systemDirs['ip_cron.php'] = 1;
        $systemDirs['index.php'] = 1;
        $systemDirs['ip_license.html'] = 1;
        $systemDirs['robots.txt'] = 1;
        $systemDirs['sitemap.php'] = 1;
        if(isset($systemDirs[$url]))
        return true;
        else
        return false;
    }

    /**
     * Some modules need to make some actions before any output.
     * This function detects such requirements and executes specified module.
     * If you need to use this feature, simply POST (or GET) two variables:
     * @private
     * $_REQUEST['module_group']
     * $_REQUEST['module_name']
     * This function will include file actions.php on specified module directory and axecute method "make_actions()" on class actions_REQUEST['module_gorup']_REQUEST['module_name']
     */
    public function makeActions() {
        if (!empty($_REQUEST) && isset($_REQUEST['module_group']) && isset($_REQUEST['module_name'])) { //old deprecated way
            //actions may be set by post or get. The prime way is trouht post. But in some cases it is not possible
            $newModule = \Db::getModule(null, $_REQUEST['module_group'], $_REQUEST['module_name']);
            if ($newModule) {
                if ($newModule['core']) {
                    require_once \Ip\Config::oldModuleFile($newModule['g_name'] . '/' . $newModule['m_name'] . '/actions.php');
                } else {
                    require_once(BASE_DIR . PLUGIN_DIR . $newModule['g_name'] . '/' . $newModule['m_name'] . '/actions.php');
                }
                eval('$tmpModule = new \\Modules\\' . $newModule['g_name'] . '\\' . $newModule['m_name'] . '\\Actions();');
                $tmpModule->makeActions();
            } else {
                $backtrace = debug_backtrace();
                if (isset($backtrace[0]['file']) && isset($backtrace[0]['line'])) {
                    trigger_error("Requested module ({$_REQUEST['module_group']}>{$_REQUEST['module_name']}) does not exitst. (Error source: {$backtrace[0]['file']} line: {$backtrace[0]['line']} ) ");
                } else {
                    trigger_error("Requested module ({$_REQUEST['module_group']}>{$_REQUEST['module_name']}) does not exitst.");
                }
            }
        }

    }


}

