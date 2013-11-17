<?php
namespace Ip\Module\Admin;


class OldCmsInterface{

    var $module;  //current module object
    var $session;
    var $tinyMce; //true if tinyMce engine is loaded
    var $curModId;
    var $loginError;

    function __construct() {
        $this->session = new OldSessionInterface();


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

        //log off
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == "logout" && !isset($_REQUEST['module_id'])) {
            $this->session->logout();
            $this->html->headerModules();
            $this->html->html('<script type="text/javascript">window.top.location=\'admin.php\';</script>');
            $this->deleteTmpFiles();
            $this->html->footer();
            $this->html->send();
            \Ip\Internal\Deprecated\db::disconnect();
            exit;
        }
        //eof log off



        //log in
        if(isset($_REQUEST['action']) && isset($_POST['f_name']) && isset($_POST['f_pass']) && $_REQUEST['action'] == "login" && !isset($_REQUEST['module_id'])) {

            if(\Ip\Internal\Db::incorrectLoginCount($_POST['f_name'].'('.$_SERVER['REMOTE_ADDR'].')') > 2) {
                $this->loginError = __('Your login suspended for one hour.', 'ipAdmin');
                \Ip\Internal\Db::log('system', 'backend login suspended', $_POST['f_name'].'('.$_SERVER['REMOTE_ADDR'].')', 2);
            }else {
                $id = \Ip\Internal\Db::userId($_POST['f_name'], $_POST['f_pass']);
                if($id !== false) {
                    $this->session->login($id);
                    \Ip\Internal\Db::log('system', 'backend login', $_POST['f_name'].' ('.$_SERVER['REMOTE_ADDR'].')', 0);
                    header("location:ip_backend_frames.php");
                } else {
                    $this->loginError = __('Incorrect name or password', 'ipAdmin');
                    \Ip\Internal\Db::log('system', 'backend login incorrect', $_POST['f_name'].'('.$_SERVER['REMOTE_ADDR'].')', 1);
                }
            }
        }
        //eof log in
        if($this->session->loggedIn()) {  //login check
            //create module
            if(isset($_GET['module_id']) && $_GET['module_id'] != '' && \Ip\Internal\Db::allowedModule($_GET['module_id'], $this->session->userId())) {
                /*new module*/
                $newModule = \Ip\Internal\Deprecated\Db::getModule($_GET['module_id']);
                if ($newModule['core']) {
                } else {
                    // TODOX Plugin dir
                }
                $this->curModId = $newModule['id'];
                eval('$this->module = new \\Modules\\'.$newModule['g_name'].'\\'.$newModule['m_name'].'\\Manager();');
            }else {
                if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'first_module') {
                    /*first module*/
                    $newModule = \Ip\Internal\Db::firstAllowedModule($this->session->userId());
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
            // TODOX write good comment
            $baseUrl = \Ip\Config::baseUrl('');

            if(strpos($baseUrl, $_SERVER['HTTP_HOST']) != 7 && strpos($baseUrl, $_SERVER['HTTP_HOST']) != 8 ) {
                /*check if we are in correct subdomain. www.yoursite.com not allways equal to yoursite.com from session perspective)*/
                header("location: ". \Ip\Config::baseUrl("admin.php"));
                \Ip\Internal\Deprecated\db::disconnect();
                exit;
            }
            $this->html->html(Template::headerLogin());
            $this->html->html('<script type="text/javascript">if(parent.header && parent.content)parent.window.top.location=\'' . \Ip\Config::baseUrl('admin.php') . '\';</script>');
            $this->html->html(Template::loginForm($this->loginError)); //login window
            $this->html->footer();
        }

        $this->html->send();

    }

    function worker() { //make worker actions.
        global $cms;
        global $globalWorker;
        if($this->session->loggedIn()) {  //login check

            if(isset($_GET['module_id']) && $_GET['module_id'] != '' && \Ip\Internal\Db::allowedModule($_GET['module_id'], $cms->session->userId())) {
                $this->curModId = $_GET['module_id'];
                $newModule = \Ip\Internal\Deprecated\Db::getModule($_GET['module_id']);

                if(Db::allowedModule($_GET['module_id'], $this->session->userId())){
                        // TODOX Plugin dir
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
        if ($moduleId == null) {
            $moduleId = $this->curModId;
        }

        if ($getVars != '') {
            return \Ip\Config::baseUrl('?admin=1&module_id='.$moduleId.'&'.$getVars.'&security_token='.$this->session->securityToken());
        } else {
            return \Ip\Config::baseUrl('?admin=1&module_id='.$moduleId.'&security_token='.$this->session->securityToken());
        }
    }


    // TODOX move to appropriate place
    function deleteTmpFiles() {
        $dirs = array();
        $dirs[] = \Ip\Config::temporaryFile('');
        $dirs[] = \Ip\Config::temporarySecureFile('');

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
                            if ($file != '.htaccess' && ($file != 'readme.txt' || $depth > 0)) {
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

        $systemDirs[\Ip\Config::getRaw('FILE_DIR')] = 1;
        $systemDirs[\Ip\Config::getRaw('INCLUDE_DIR')] = 1;
        $systemDirs[\Ip\Config::getRaw('LIBRARY_DIR')] = 1;
        $systemDirs[\Ip\Config::getRaw('MODULE_DIR')] = 1;
        $systemDirs[\Ip\Config::getRaw('THEME_DIR')] = 1;
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
        if(sizeof($_REQUEST) > 0) {
            if(isset($_REQUEST['module_group']) && isset($_REQUEST['module_name'])) { //old deprecated way
                //actions may be set by post or get. The prime way is trouht post. But in some cases it is not possible
                $newModule = \Ip\Internal\Deprecated\Db::getModule(null, $_REQUEST['module_group'], $_REQUEST['module_name']);
                if($newModule) {
                    if($newModule['core']) {
                    } else {
                        //require_once(BASE_DIR.PLUGIN_DIR.$newModule['g_name'].'/'.$newModule['m_name'].'/actions.php');
                    }
                    eval('$tmpModule = new \\Modules\\'.$newModule['g_name'].'\\'.$newModule['m_name'].'\\Actions();');
                    $tmpModule->makeActions();
                }else {
                    $backtrace = debug_backtrace();
                    if(isset($backtrace[0]['file']) && isset($backtrace[0]['line']))
                        trigger_error("Requested module (".$_REQUEST['module_group'].">".$_REQUEST['module_name'].") does not exitst. (Error source: '.$backtrace[0]['file'].' line: '.$backtrace[0]['line'].' ) ");
                    else
                        trigger_error("Requested module (".$_REQUEST['module_group'].">".$_REQUEST['module_name'].") does not exitst.");
                }
            }
        }

    }


}

