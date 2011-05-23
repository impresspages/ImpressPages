<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */
namespace update_1_0_8_to_1_0_9;

if (!defined('CMS')) exit;

require_once('translations.php');

class Script {
    var $deleteFiles;
    var $addFiles;
    var $deleteFolders;
    var $addFolders;

    var $stepCount;
    var $curStep;
    var $curAction;


    public function __construct($stepCount, $curStep, $curAction) {
        $this->deleteFolders = array();
        $this->deleteFolders[] = 'install';
        $this->deleteFolders[] = 'ip_cms';
        $this->deleteFolders[] = 'ip_libs';


        $this->deleteFiles = array();
        $this->deleteFiles[] = 'admin.php';
        $this->deleteFiles[] = 'index.php';
        $this->deleteFiles[] = 'ip_backend_frames.php';
        $this->deleteFiles[] = 'ip_backend_worker.php';
        $this->deleteFiles[] = 'ip_cron.php';
        $this->deleteFiles[] = 'ip_license.html';
        $this->deleteFiles[] = 'sitemap.php';

        $this->addFolders = array();
        $this->addFolders[] = 'ip_cms';
        $this->addFolders[] = 'ip_libs';
        $this->addFolders[] = 'ip_plugins';

        $this->addFiles = array();
        $this->addFiles[] = 'admin.php';
        $this->addFiles[] = 'index.php';
        $this->addFiles[] = 'ip_backend_frames.php';
        $this->addFiles[] = 'ip_backend_worker.php';
        $this->addFiles[] = 'ip_cron.php';
        $this->addFiles[] = 'ip_license.html';
        $this->addFiles[] = 'sitemap.php';

        $this->stepCount = $stepCount;
        $this->curStep = $curStep;
        $this->curAction = $curAction;
    }

    public function getActionsCount() {
        return 3;
    }

    public function process () {
        global $htmlOutput;
        global $navigation;

        $answer = '';


        switch ($this->curAction) {
            default:
            case 1:
                $answer .= $this->filesToDelete();
                break;
            case 2:
                $answer .= $this->filesToUpload();
                break;
            case 3:
                $answer .= $this->updateDatabase();
                break;
        }


        return $answer;
    }



    public function needToDelete() {
        $answer = false;
        if($this->curStep == 1 && !isset($_SESSION['process'][1]['deleted'])) {
            foreach ($this->deleteFolders as $folder){
                if (is_dir('../'.$folder) ) {
                    $answer = true;
                }
            }
            foreach ($this->deleteFiles as $file){
                if (is_file('../'.$file) ) {
                    $answer = true;
                }
            }

            if ($answer == false) {
                $_SESSION['process'][1]['deleted'] = true;
            }
        }
        return $answer;
    }

    public function needToUpload() {
        $answer = false;
        if($this->curStep == $this->stepCount && !isset($_SESSION['process'][1]['uploaded'])) {
            foreach ($this->addFolders as $folder){
                if (!is_dir('../'.$folder) ) {
                    $answer = true;
                }
            }
            foreach ($this->addFiles as $file){
                if (!is_file('../'.$file) ) {
                    $answer = true;
                }
            }

            if ($answer == false) {
                $_SESSION['process'][1]['uploaded'] = true;
            }
        }
        return $answer;
    }

    public function filesToDelete() {
        global $navigation;
        global $htmlOutput;

        $answer = '';

        $tableFolders = array();

        foreach ($this->deleteFolders as $folder){
            if (is_dir('../'.$folder) ) {
                $tableFolders[] = '/'.$folder.'/';
                $tableFolders[] = '';
            }
        }


        if (sizeof($tableFolders)) {
            $answer .= REMOVE_DIRECTORIES.$htmlOutput->table($tableFolders);
            $answer .= '<br/>';
        }



        $tableFiles = array();
        foreach ($this->deleteFiles as $file){
            if (is_file('../'.$file) ) {
                $tableFiles[] = '/'.$file;
                $tableFiles[] = '';
            }
        }

        if (sizeof($tableFiles)) {
            $answer .= REMOVE_FILES.$htmlOutput->table($tableFiles);
        }

        if ($this->needToDelete())
        $answer .= $htmlOutput->button(IP_NEXT, $navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction()));
        else {
            header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction() + 1));
        }

        return $answer;
    }

    public function filesToUpload(){
        global $navigation;
        global $htmlOutput;

        $answer = '';

        $tableFolders = array();

        foreach ($this->addFolders as $folder){
            if (!is_dir('../'.$folder) ) {
                $tableFolders[] = '/'.$folder.'/';
                $tableFolders[] = '';
            }
        }


        if (sizeof($tableFolders)) {
            $answer .= UPLOAD_DIRECTORIES.$htmlOutput->table($tableFolders);
            $answer .= '<br/>';
        }



        $tableFiles = array();
        foreach ($this->addFiles as $file){
            if (!is_file('../'.$file) ) {
                $tableFiles[] = '/'.$file;
                $tableFiles[] = '';
            }
        }

        if (sizeof($tableFiles)) {
            $answer .= UPLOAD_FILES.$htmlOutput->table($tableFiles);
        }

        if ($this->needToUpload())
        $answer .= $htmlOutput->button(IP_NEXT, $navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction()));
        else {
            header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction() + 1));
        }
        return $answer;
    }





    public function updateDatabase() {
        global $navigation;
        global $htmlOutput;
        require_once(__DIR__.'/../update_1_0_2_beta_to_1_0_3_beta/parameters_refractor.php');
        
        $answer = '';
        if (\Db_100::getSystemVariable('version') != '1.0.9') {

            $sql = "ALTER TABLE `".DB_PREF."content_element` CHANGE `row_number` `row_number` DOUBLE NOT NULL DEFAULT '0' ";
            $rs = mysql_query($sql);
            if (!$sql) {
                trigger_error($sql.' '.mysql_error());
            }
            
            $module = \Db_100::getModule(null, 'standard', 'menu_management');

            $parametersRefractor = new \update_1_0_2_beta_to_1_0_3_beta\ParametersRefractor();
            
            $group = $parametersRefractor->getParametersGroup($module['id'], 'admin_translations');
            if ($group) {
                if(!\Db_100::getParameter('standard', 'menu_management', 'admin_translations', 'general')) {
                    \Db_100::addStringParameter($group['id'], 'General', 'general', 'General', 1);
                }
                if(!\Db_100::getParameter('standard', 'menu_management', 'admin_translations', 'seo')) {
                    \Db_100::addStringParameter($group['id'], 'Seo', 'seo', 'SEO', 1);
                }
                if(!\Db_100::getParameter('standard', 'menu_management', 'admin_translations', 'advanced')) {
                    \Db_100::addStringParameter($group['id'], 'Advanced', 'advanced', 'Advanced', 1);
                }
                if(!\Db_100::getParameter('standard', 'menu_management', 'admin_translations', 'copy')) {
                    \Db_100::addStringParameter($group['id'], 'Copy', 'copy', 'Copy', 1);
                }
                if(!\Db_100::getParameter('standard', 'menu_management', 'admin_translations', 'paste')) {
                    \Db_100::addStringParameter($group['id'], 'Paste', 'paste', 'Paste', 1);
                }
                if(!\Db_100::getParameter('standard', 'menu_management', 'admin_translations', 'cancel')) {
                    \Db_100::addStringParameter($group['id'], 'Cancel', 'cancel', 'Cancel', 1);
                }
            }
            
            
            if ($this->curStep == $this->stepCount){
                \Db_100::setSystemVariable('version','1.0.9rc3');
            }
        }

        if ($this->curStep == $this->stepCount) {
            header("location: ".$navigation->generateLink($navigation->curStep() + 1));
        } else {
            header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript() + 1));
        }

        return $answer;
    }






}

