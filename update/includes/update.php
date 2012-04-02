<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2012 ImpressPages LTD.
 * @license see ip_license.html
 */


if (!defined('CMS')) exit;

require_once('db/db100.php');
require_once('db/parameters_refractor.php');


class Update {
    private static $connection;

    public function execute() {
        global $navigation;
        global $htmlOutput;

        $this->includeConfig();
        $this->dbConnect();

        $step = $navigation->curStep();

        $page = '';

        $error = $this->findError();

        if ($error == false) {
            switch ($step) {
                case 1:
                    $page .= $htmlOutput->header();
                    $page .= $this->stepBackup();
                    $page .= $htmlOutput->footer();
                    break;
                case 2:
                    $page .= $htmlOutput->header();
                    $page .= $this->stepProcess();
                    $page .= $htmlOutput->footer();
                    break;
                case 3:
                    $page .= $htmlOutput->header();
                    $page .= $this->stepFinish();
                    $page .= $htmlOutput->footer();
                    break;
                default:
                    $page .= $htmlOutput->header();
                    $page .= $this->stepSystemCheck();
                    $page .= $htmlOutput->footer();
                    break;
            }
        } else {
            $page .= $htmlOutput->header();
            $page .= $error;
            $page .= $htmlOutput->footer();
        }



        $this->dbDisconnect();
        echo $page;
    }

    private function findError() {
        global $scripts;
        global $navigation;
        $answer = false;

        if ($this->getCurrentVersion() == $scripts::destinationVersion) {
            return IP_ERROR_COMPLETED;
        }

        
        $script = $navigation->curScript();
        $action = $navigation->curAction();
        $breadcrumb = $scripts->getScripts($this->getCurrentVersion());
        if (sizeof($breadcrumb) == 0) {
            return IP_ERROR_NO_INFORMATION;
        }
        
        
        if ($navigation->curStep() == 2 && sizeof($breadcrumb) < $script) {
            return IP_ERROR_404;
        }
        
        
        $curScript = $breadcrumb[$script - 1];
        require_once('scripts/'.$curScript['script'].'/script.php');
        eval ('$tmpScript = new \\'.$curScript['script'].'\\Script(sizeof($breadcrumb), $script, $action); ');
        if ($tmpScript->getActionsCount() < $action) {
            return IP_ERROR_404;
        }
        
        
        $module = \Db_100::getModule(null, 'standard', 'copy_content');
        if ($module) {
            return IP_ERROR_UNINSTALL_COPY_CONTENT;
        }
        
        
        //add check if copy content is not present        
        

        return $answer;
    }

    private function stepbackup() {
        global $htmlOutput;
        global $navigation;

        require_once('includes/scripts.php');

        $answer = '';

        $answer .= $htmlOutput->h1(IP_STEP_BACKUP);

        $tmpHtml = IP_STEP_BACKUP_INTRODUCTION;

        $tmpHtml = str_replace("[[current_version]]", htmlspecialchars($this->getCurrentVersion()), $tmpHtml);
        $tmpHtml = str_replace("[[new_version]]", htmlspecialchars(Scripts::destinationVersion), $tmpHtml);

        $answer .= $tmpHtml;

        if ($this->getCurrentVersion() == '2.0rc1' || $this->getCurrentVersion() == '2.0rc2') {
            $tmpHtml = IP_OLD_VERSION_VARNING;
            $tmpHtml = str_replace("[[current_version]]", htmlspecialchars($this->getCurrentVersion()), $tmpHtml);
            $answer .= $tmpHtml;
        }
        if ($this->getCurrentVersion() == '2.0rc1' || $this->getCurrentVersion() == '2.0rc2' || $this->getCurrentVersion() == '2.0') {
            $tmpHtml = IP_OLD_VERSION_VARNING2;
            $tmpHtml = str_replace("[[current_version]]", htmlspecialchars($this->getCurrentVersion()), $tmpHtml);
            $answer .= $tmpHtml;
        }
        
        
        $answer .= "<br/><br/>";

        $answer .= $htmlOutput->button(IP_STEP_BACKUP_UPDATE, $navigation->generateLink(2));

        return $answer;
    }

    private function stepProcess () {
        global $htmlOutput;
        global $navigation;
        global $scripts;
        $answer = '';

        $answer .= $htmlOutput->h1(IP_STEP_PROCESS);

        $script = $navigation->curScript();
        $action = $navigation->curAction();
        $breadcrumb = $scripts->getScripts($this->getCurrentVersion());
        $curScript = $breadcrumb[$script - 1];
        require_once('scripts/'.$curScript['script'].'/script.php');
        eval ('$tmpScript = new \\'.$curScript['script'].'\\Script(sizeof($breadcrumb), $script, $action); ');
        $answer .= $tmpScript->process();


        return $answer;
    }

    private function stepFinish () {

    }


    private function dbConnect() {
        //db connect
        self::$connection = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
        if(!self::$connection) {
            trigger_error('Can\'t connect to database.');
            return false;
        }else {
            mysql_select_db(DB_DATABASE);
            mysql_query("SET CHARACTER SET ".MYSQL_CHARSET);
        }
        //db connect
    }

    private function dbDisconnect() {
        mysql_close(self::$connection);
    }

    private function includeConfig() {
        if(file_exists('../includes/config.php')) {
            require_once('../includes/config.php');
        } else {
            if(file_exists('../config.php')) {
                require_once('../config.php');
            } else {
                require_once('../ip_config.php');
            }
        }
    }

    private function getCurrentVersion () {
        $answer = false;

        $answer = \Db_100::getSystemVariable('version');

        return $answer;
    }


}





