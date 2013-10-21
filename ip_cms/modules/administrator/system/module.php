<?php

/**
 * @package   ImpressPages
 *
 *
 */


namespace Modules\administrator\system;



require_once (BASE_DIR . INCLUDE_DIR . 'db_system.php');
require_once (BASE_DIR . MODULE_DIR . 'developer/modules/db.php');

class Module
{
    /**
     * @param string $oldUrl
     * @return bool true on success
     */
    public function updateRobotsTxt($oldUrl)
    {
        $robotsFile = 'robots.txt';
        if ($oldUrl != BASE_URL && file_exists($robotsFile)) { //update robots.txt file.
            $data = file($robotsFile, FILE_IGNORE_NEW_LINES);
            $newData = '';
            foreach ($data as $dataKey => $dataVal) {
                $tmpVal = $dataVal;
                $tmpVal = trim($tmpVal);

                $tmpVal = preg_replace('/^Sitemap:(.*)/', 'Sitemap: ' . BASE_URL . 'sitemap.php', $tmpVal);
                $newData .= $tmpVal . "\n";
            }
            if (is_writable($robotsFile)) {
                file_put_contents($robotsFile, $newData);
                return true;
            } else {
                return false;
            }
        }
        return true;
    }


    public function clearCache($cachedUrl)
    {
        global $cms;
        global $parametersMod;
        global $site;
        global $dispatcher;




        $sql = "select m.name as m_name, mg.name as mg_name from `" . DB_PREF . "module_group` mg, `" . DB_PREF . "module` m where m.group_id = mg.id";
        $rs = mysql_query($sql);
        if ($rs) {
            while ($lock = mysql_fetch_assoc($rs)) {

                $systemFileExists = false;
                if (file_exists(BASE_DIR . MODULE_DIR . $lock['mg_name'] . '/' . $lock['m_name'] . "/system.php")) {
                    require_once(BASE_DIR . MODULE_DIR . $lock['mg_name'] . '/' . $lock['m_name'] . "/system.php");
                    $systemFileExists = true;
                }

                if (!$systemFileExists && file_exists(BASE_DIR . MODULE_DIR . $lock['mg_name'] . '/' . $lock['m_name'] . "/System.php")) {
                    require_once(BASE_DIR . MODULE_DIR . $lock['mg_name'] . '/' . $lock['m_name'] . "/System.php");
                    $systemFileExists = true;
                }

                if ($systemFileExists) {
                    eval('$module_system = new \\Modules\\' . $lock['mg_name'] . '\\' . $lock['m_name'] . '\\System();');
                    if (method_exists($module_system, 'clearCache')) {
                        $module_system->clearCache($cachedUrl);
                    }
                }
            }

        }

        \DbSystem::setSystemVariable('cached_base_url', BASE_URL); // update system variable


        $cacheVersion = \DbSystem::getSystemVariable('cache_version');
        \DbSystem::setSystemVariable('cache_version', $cacheVersion + 1);



        //throw event in 2.X style
        $dispatcher->notify(new \Ip\Event\UrlChanged($this, $cachedUrl, BASE_URL));

        $dispatcher->notify(new \Ip\Event\ClearCache($this));


    }

    public function getSystemInfo()
    {

        $answer = '';

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, \Modules\administrator\system\Model::instance()->getImpressPagesAPIUrl());
            curl_setopt($ch, CURLOPT_POST, 1);

            $postFields = 'module_name=communication&module_group=service&action=getInfo&version=1&afterLogin=';
            $postFields .= '&systemVersion=' . \DbSystem::getSystemVariable('version');

            $groups = \Modules\developer\modules\Db::getGroups();
            foreach ($groups as $groupKey => $group) {
                $modules = \Modules\developer\modules\Db::getModules($group['id']);
                foreach ($modules as $moduleKey => $module) {
                    $postFields .= '&modules[' . $group['name'] . '][' . $module['name'] . ']=' . $module['version'];
                }
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_REFERER, BASE_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            $answer = curl_exec($ch);

            if (json_decode($answer) === null) { //json decode error
                return '';
            }


        }

        return $answer;
    }


    public function getUpdateInfo()
    {
        if (!function_exists('curl_init')) {
            return false;
        }

        $ch = curl_init();

        $curVersion = \DbSystem::getSystemVariable('version');

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 1800, // set this to 30 min so we dont timeout
            CURLOPT_URL => \Modules\administrator\system\Model::instance()->getImpressPagesAPIUrl(),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => 'module_group=service&module_name=communication&action=getUpdateInfo&curVersion='.$curVersion
        );

        curl_setopt_array($ch, $options);

        $jsonAnswer = curl_exec($ch);

        $answer = json_decode($jsonAnswer, true);

        if ($answer === null || !isset($answer['status']) || $answer['status'] != 'success') {
            return false;
        }

        return $answer;
    }

}

