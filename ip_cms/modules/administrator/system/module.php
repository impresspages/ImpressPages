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

class Module{
  
  function clearCache(){
    global $cms;
    global $parametersMod;
    global $site;
    

    
    $cachedUrl = \DbSystem::getSystemVariable('cached_base_url'); // get system variable

    $site->dispatchEvent('administrator', 'system', 'clear_cache', array('old_url'=>$cachedUrl, 'new_url'=>BASE_URL));
    
    if ($cachedUrl != BASE_URL) { //update robots.txt file.
      $robotsFile = 'robots.txt';
      $data = file($robotsFile, FILE_IGNORE_NEW_LINES);
      $newData = '';
      foreach($data as $dataKey => $dataVal) {
        $tmpVal = $dataVal;
        $tmpVal = trim($tmpVal);

        $tmpVal =  preg_replace('/^Sitemap:(.*)/', 'Sitemap: '.BASE_URL.'sitemap.php', $tmpVal);
        $newData .= $tmpVal."\n";
      }
      file_put_contents($robotsFile, $newData);          
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
      
      \DbSystem::replaceUrls($cachedUrl, BASE_URL);           
      \DbSystem::setSystemVariable('cached_base_url', BASE_URL); // update system variable
      
          
    }         
    $site->dispatchEvent('administrator', 'system', 'cache_cleared', array('old_url'=>$cachedUrl, 'new_url'=>BASE_URL));
    
   
  }
}

