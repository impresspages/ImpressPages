<?php

/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */


namespace Modules\administrator\system;

if (!defined('BACKEND')) exit;

require_once (__DIR__.'/html_output.php');
require_once (BASE_DIR.INCLUDE_DIR.'db_system.php');
require_once (BASE_DIR.MODULE_DIR.'developer/modules/db.php');

class Module{

    function clearCache(){
        global $cms;
        global $parametersMod;
        global $site;
        global $dispatcher;


        //throw event in 1.0.X style
        $cachedUrl = \DbSystem::getSystemVariable('cached_base_url'); // get system variable

        $site->dispatchEvent('administrator', 'system', 'clear_cache', array('old_url'=>$cachedUrl, 'new_url'=>BASE_URL));
        
        

        $errors = false;
    
        $robotsFile = 'robots.txt';
        if ($cachedUrl != BASE_URL && file_exists($robotsFile)) { //update robots.txt file.
            $data = file($robotsFile, FILE_IGNORE_NEW_LINES);
            $newData = '';
            foreach($data as $dataKey => $dataVal) {
                $tmpVal = $dataVal;
                $tmpVal = trim($tmpVal);

                $tmpVal =  preg_replace('/^Sitemap:(.*)/', 'Sitemap: '.BASE_URL.'sitemap.php', $tmpVal);
                $newData .= $tmpVal."\n";
            }
            if (is_writable($robotsFile)) {
                file_put_contents($robotsFile, $newData);
            } else {
                trigger_error('robots.txt file need to be updated. Do it manually or make it writable and clear cache once again.');
                $errors = true;
            }
        }


        $sql = "select m.name as m_name, mg.name as mg_name from `".DB_PREF."module_group` mg, `".DB_PREF."module` m where m.group_id = mg.id";
        $rs = mysql_query($sql);
        if($rs){
            while($lock = mysql_fetch_assoc($rs)){
                if(file_exists(BASE_DIR.MODULE_DIR.$lock['mg_name'].'/'.$lock['m_name']."/system.php")){
                    require_once(BASE_DIR.MODULE_DIR.$lock['mg_name'].'/'.$lock['m_name']."/system.php");
                    eval('$module_system = new \\Modules\\'.$lock['mg_name'].'\\'.$lock['m_name'].'\\System();');
                    if(method_exists($module_system, 'clearCache')){
                        $module_system->clearCache($cachedUrl);
                    }
                }
            }

      if ($errors == false) {
            \DbSystem::replaceUrls($cachedUrl, BASE_URL);
            \DbSystem::setSystemVariable('cached_base_url', BASE_URL); // update system variable
      }


        }
        
        $eventData = array(
            'old_url'=>$cachedUrl,
            'new_url'=>BASE_URL
        );

        if ($errors == false) {
            $site->dispatchEvent('administrator', 'system', 'cache_cleared', $eventData);
        }

        //throw event in 2.X style
        $dispatcher->notify(new \Ip\Event\ClearCache($this, $cachedUrl, BASE_URL));
        
         
    }

    public function getSystemInfo() {

        $answer = '';

        if(function_exists('curl_init')){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://service.impresspages.org');
            curl_setopt($ch, CURLOPT_POST, 1);

            $postFields = 'module_name=communication&module_group=service&action=getInfo&version=1&afterLogin=';
            $postFields .= '&systemVersion='.\DbSystem::getSystemVariable('version');

            $groups = \Modules\developer\modules\Db::getGroups();
            foreach($groups as $groupKey => $group){
                $modules = \Modules\developer\modules\Db::getModules($group['id']);
                foreach($modules as $moduleKey => $module){
                    $postFields .= '&modules['.$group['name'].']['.$module['name'].']='.$module['version'];
                }
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_REFERER, BASE_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            $answer = curl_exec($ch);

            if(json_decode($answer) === null) { //json decode error
                return '';
            }



        }

        return $answer;
    }
}

