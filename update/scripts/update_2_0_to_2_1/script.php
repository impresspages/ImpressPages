<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */
namespace update_2_0_to_2_1;

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
        
        $answer = '';
        if (\Db_100::getSystemVariable('version') != '2.1') {
            
            
            $parametersRefractor = new \ParametersRefractor();
            
            $parametersRefractor->deleteParameter('standard', 'content_management', 'widget_faq', 'title');
            $parametersRefractor->deleteParameter('standard', 'content_management', 'widget_faq', 'text');
            
            $module = \Db_100::getModule(null, 'standard', 'content_management');
            
            $group = $parametersRefractor->getParametersGroup($module['id'], 'widget_faq');
            if ($group) {
                if(!\Db_100::getParameter('standard', 'content_management', 'widget_faq', 'question')) {
                    \Db_100::addStringParameter($group['id'], 'Question', 'question', 'Question', 1);
                }
                if(!\Db_100::getParameter('standard', 'content_management', 'admin_translations', 'answer')) {
                    \Db_100::addStringParameter($group['id'], 'Answer', 'answer', 'Answer', 1);
                }
            }

            $group = $parametersRefractor->getParametersGroup($module['id'], 'widget_contact_form');
            if ($group) {
                if(!\Db_100::getParameter('standard', 'content_management', 'widget_contact_form', 'move')) {
                    \Db_100::addStringParameter($group['id'], 'Move', 'move', 'Move', 1);
                }
                if(!\Db_100::getParameter('standard', 'content_management', 'widget_contact_form', 'remove')) {
                    \Db_100::addStringParameter($group['id'], 'Remove', 'remove', 'Remove', 1);
                }
                
                if(!\Db_100::getParameter('standard', 'content_management', 'widget_contact_form', 'remove')) {
                    \Db_100::addParameter($group['id'], array('name' => 'send', 'translation' => 'Send', 'admin' => 0, 'type'=> 'lang', 'value' => 'Send'));
                }
                
            }
            

            
            if ($this->curStep == $this->stepCount){
                \Db_100::setSystemVariable('version','2.1');
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

