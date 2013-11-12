<?php

/**
 * @package   ImpressPages
 *
 *
 */


namespace Ip\Module\System;


class Module
{
    /**
     * @param string $oldUrl
     * @return bool true on success
     */
    public function updateRobotsTxt($oldUrl)
    {
        $robotsFile = 'robots.txt';
        if ($oldUrl != \Ip\Config::baseUrl('') && file_exists($robotsFile)) { //update robots.txt file.
            $data = file($robotsFile, FILE_IGNORE_NEW_LINES);
            $newData = '';
            foreach ($data as $dataKey => $dataVal) {
                $tmpVal = $dataVal;
                $tmpVal = trim($tmpVal);

                $tmpVal = preg_replace('/^Sitemap:(.*)/', 'Sitemap: ' . \Ip\Config::baseUrl('sitemap.php'), $tmpVal);
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
        $rs = ip_deprecated_mysql_query($sql);
        if ($rs) {
            while ($lock = ip_deprecated_mysql_fetch_assoc($rs)) {

                $systemFileExists = false;
                if (file_exists(\Ip\Config::oldModuleFile($lock['mg_name'] . '/' . $lock['m_name'] . "/system.php"))) {
                    require_once \Ip\Config::oldModuleFile($lock['mg_name'] . '/' . $lock['m_name'] . "/system.php");
                    $systemFileExists = true;
                }

                if (!$systemFileExists && file_exists(\Ip\Config::oldModuleFile($lock['mg_name'] . '/' . $lock['m_name'] . "/System.php"))) {
                    require_once \Ip\Config::oldModuleFile($lock['mg_name'] . '/' . $lock['m_name'] . "/System.php");
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

        \Ip\DbSystem::setSystemVariable('cached_base_url', \Ip\Config::baseUrl('')); // update system variable


        $cacheVersion = \Ip\DbSystem::getSystemVariable('cache_version');
        \Ip\DbSystem::setSystemVariable('cache_version', $cacheVersion + 1);



        //throw event in 2.X style
        $dispatcher->notify(new \Ip\Event\UrlChanged($this, $cachedUrl, \Ip\Config::baseUrl('')));

        $dispatcher->notify(new \Ip\Event\ClearCache($this));


    }

    public function getSystemInfo()
    {

        $answer = '';

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, \Ip\Module\System\Model::instance()->getImpressPagesAPIUrl());
            curl_setopt($ch, CURLOPT_POST, 1);

            $postFields = 'module_name=communication&module_group=service&action=getInfo&version=1&afterLogin=';
            $postFields .= '&systemVersion=' . \Ip\DbSystem::getSystemVariable('version');

            //TODOX refactor
//            $groups = \Modules\developer\modules\Db::getGroups();
//            foreach ($groups as $groupKey => $group) {
//                $modules = \Modules\developer\modules\Db::getModules($group['id']);
//                foreach ($modules as $moduleKey => $module) {
//                    $postFields .= '&modules[' . $group['name'] . '][' . $module['name'] . ']=' . $module['version'];
//                }
//            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_REFERER, \Ip\Config::baseUrl(''));
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

        $curVersion = \Ip\DbSystem::getSystemVariable('version');

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 1800, // set this to 30 min so we dont timeout
            CURLOPT_URL => \Ip\Module\System\Model::instance()->getImpressPagesAPIUrl(),
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

