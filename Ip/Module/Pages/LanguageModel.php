<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Module\Pages;

class LanguageModel{

    public function updateLanguage($languageId, $data) {

        $condition = array(
            'id' => $languageId
        );

        $language = $this->getLanguageByUrl($data['url']);
        if ($language && $language['id'] != $languageId) {
            throw new DuplicateUrlException($data['url']);
        }

        $originalLanguage = self::getLanguageById($languageId);
        $originalUrl = ipGetConfig()->baseUrl($originalLanguage['url']) . '/';

        \Ip\Db::update(DB_PREF . 'language', $data, $condition);

        $newUrl = ipGetConfig()->baseUrl($data['url']) . '/';

        if ($originalUrl != $newUrl){
            ipDispatcher()->notify('site.urlChanged', array('oldUrl' => $originalUrl, 'newUrl' => $newUrl));
        }
    }

    private static function afterInsert($id) {
        self::createRootZoneElement($id);
    }

    private function afterDelete($id) {
        self::deleteRootZoneElement($id);
    }


    private function beforeUpdate($id) {
        $tmpLanguage = self::getLanguageById($id);
        $this->urlBeforeUpdate = $tmpLanguage['url'];
    }


    private function afterUpdate($id) {
        global $parametersMod;

        $tmpLanguage = self::getLanguageById($id);
        if($tmpLanguage['url'] != $this->urlBeforeUpdate && $parametersMod->getValue('standard', 'languages', 'options', 'multilingual')) {
            $oldUrl = BASE_URL.$this->urlBeforeUpdate.'/';
            $newUrl = BASE_URL.$tmpLanguage['url'].'/';
            ipDispatcher()->notify('site.urlChanged', array('oldUrl' => $oldUrl, 'newUrl' => $newUrl));
        }
    }

    private function allowDelete($id) {
        $dbMenuManagement = new Db();

        $answer = true;


        $zones = self::getZones();
        foreach($zones as $key => $zone) {
            $rootElement = $dbMenuManagement->rootContentElement($zone['id'], $id);
            $elements = $dbMenuManagement->pageChildren($rootElement);
            if(sizeof($elements) > 0) {
                $answer = false;
                $this->errors['delete'] = __('Can\'t delete language with existing content in it', 'ipAdmin');
            }
        }

        if(sizeof(self::getLanguages()) ==1) {
            $answer = false;
            $this->errors['delete'] = __('Can\'t delete last language', 'ipAdmin');
        }


        return $answer;
    }



    private function getLanguages() {
        $answer = array();
        $sql = "select * from `".DB_PREF."language` where 1 order by row_number";
        $rs = mysql_query($sql);
        if($rs) {
            while($lock = mysql_fetch_assoc($rs))
                $answer[] = $lock;
        }else {
            trigger_error($sql." ".mysql_error());
        }
        return $answer;
    }

    private function getLanguageById($id) {
        $sql = "
            SELECT
                *
            FROM
                `".DB_PREF."language`
            WHERE
                `id` = :id ";
        $params = array (
            'id' => $id
        );
        $result = \Ip\Db::fetchRow($sql, $params);
        return $result;
    }

    private function getLanguageByUrl($url) {
        $sql = "
            SELECT
                *
            FROM
                `".DB_PREF."language`
            WHERE
                `url` = :url ";
        $params = array (
            'url' => $url
        );
        $result = \Ip\Db::fetchRow($sql, $params);
        return $result;
    }

    private function getZones() {
        $sql = "
            SELECT
                *
            FROM
                `".DB_PREF."zone`
            WHERE
                1
            ORDER BY
                `row_number`";
        $params = array ();
        $result = \Ip\Db::fetchAll($sql, $params);
        return $result;
    }

    private function deleteRootZoneElement($language) {
        $zones = self::getZones();
        foreach($zones as $key => $zone) {

            $sql = "delete `".DB_PREF."content_element`.*, `".DB_PREF."zone_to_content`.* from `".DB_PREF."content_element`, `".DB_PREF."zone_to_content` where
      `".DB_PREF."zone_to_content`.zone_id = ".$zone['id']." and `".DB_PREF."zone_to_content`.element_id = `".DB_PREF."content_element`.id and `".DB_PREF."zone_to_content`.language_id = '".mysql_real_escape_string($language)."'";
            $rs = mysql_query($sql);
            if(!$rs) {
                trigger_error($sql." ".mysql_error());
            }

            $sql2 = "delete from `".DB_PREF."zone_parameter` where language_id = '".mysql_real_escape_string($language)."'";
            $rs2 = mysql_query($sql2);
            if(!$rs2)
                trigger_error($sql2." ".mysql_error());

        }


    }

    private function createRootZoneElement($language) {
        $firstLanguage = \Ip\Internal\ContentDb::getFirstLanguage();
        $zones = \Ip\Internal\ContentDb::getZones($firstLanguage['id']);
        foreach($zones as $key => $zone) {
            $sql2 = "insert into `".DB_PREF."zone_parameter` set
        language_id = '".mysql_real_escape_string($language)."',
        zone_id = '".$zone['id']."',
        title = '".mysql_real_escape_string($this->newUrl($language, $zone['title']))."',
        url = '".mysql_real_escape_string($this->newUrl($language, $zone['url']))."'";
            $rs2 = mysql_query($sql2);
            if(!$rs2)
                trigger_error($sql2." ".mysql_error());
        }
    }


    private function newUrl($language, $url = 'zone') {
        $sql = "select url from `".DB_PREF."zone_parameter` where `language_id` = '".mysql_real_escape_string($language)."' ";
        $rs = mysql_query($sql);
        if($rs) {
            $urls = array();
            while($lock = mysql_fetch_assoc($rs))
                $urls[$lock['url']] = 1;

            if (isset($urls[$url])) {
                $i = 1;
                while(isset($urls[$url.$i])) {
                    $i++;
                }
                return $url.$i;
            } else {
                return $url;
            }
        }else {
            trigger_error("Can't get all urls ".$sql." ");
        }
    }




}

