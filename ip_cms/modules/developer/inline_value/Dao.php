<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace Modules\developer\inline_value;


class Dao
{
    private $module;
    private $lastValueScope;


    /**
     * @param string $module
     */
    public function __construct($module)
    {
        $this->module = $module;
    }

    // GET
    public function getValue($key, $languageId, $zoneName, $pageId)
    {
        global $site;

        //Find value in breadcrumb
        $breadcrumb = $site->getBreadcrumb($zoneName, $pageId);
        array_reverse($breadcrumb);

        foreach ($breadcrumb as $position => $element) {
            $value = $this->getPageValue($key, $zoneName, $element->getId());
            if ($value !== false) {
                if ($position == 0) {
                    $scope = new Entity\Scope();
                    $scope->settype(Entity\Scope::SCOPE_PAGE);
                    $scope->setPageId($element->getId());
                    $scope->setZoneName($zoneName);
                    $this->lastValueScope = $scope;
                } else {
                    $scope = new Entity\Scope();
                    $scope->settype(Entity\Scope::SCOPE_PARENT_PAGE);
                    $scope->setPageId($element->getId());
                    $scope->setZoneName($zoneName);
                    $this->lastValueScope = $scope;
                }
                return $value;
            }
        }

        //Find language value
        $value = $this->getLanguageValue($key, $languageId);
        if ($value !== false) {
            $scope = new Entity\Scope();
            $scope->settype(Entity\Scope::SCOPE_LANGUAGE);
            $scope->setLanguageId($languageId);
            $this->lastValueScope = $scope;
            return $value;
        }

        //Find global value
        $value = $this->getGlobalValue($key);
        if ($value !== false) {
            $scope = new Entity\Scope();
            $scope->settype(Entity\Scope::SCOPE_GLOBAL);
            $this->lastValueScope = $scope;
            return $value;
        }

        $this->lastValueScope = false;
        return false;
    }

    /**
     * Last get operation scope
     * @return int
     */
    public function getLastOperationScope()
    {
        return $this->lastValueScope;
    }


    public function getPageValue($key, $zoneName, $pageId)
    {
        $this->lastValueScope = Service::SCOPE_PAGE;

        $dbh = \Ip\Db::getConnection();
        $sql = '
            SELECT
                value
            FROM
                `'.DB_PREF.'m_inline_value_page`
            WHERE
                `module` = :module AND
                `key` = :key AND
                `zoneName` = :zoneName AND
                `pageId` = :pageId
        ';

        $params = array (
            ':module' => $this->module,
            ':key' => $key,
            ':zoneName' => $zoneName,
            ':pageId' => $pageId
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
        if ($lock = $q->fetch(\PDO::FETCH_ASSOC)) {
            return $lock['value'];
        } else {
            return false;
        }
    }

    public function getLanguageValue($key, $languageId)
    {
        $this->lastValueScope = Service::SCOPE_LANGUAGE;

        $dbh = \Ip\Db::getConnection();
        $sql = '
            SELECT
                value
            FROM
                `'.DB_PREF.'m_inline_value_language`
            WHERE
                `module` = :module AND
                `key` = :key AND
                `languageId` = :languageId
        ';

        $params = array (
            ':module' => $this->module,
            ':key' => $key,
            ':languageId' => $languageId
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
        if ($lock = $q->fetch(\PDO::FETCH_ASSOC)) {
            return $lock['value'];
        } else {
            return false;
        }
    }

    public function getGlobalValue($key)
    {
        $this->lastValueScope = Service::SCOPE_GLOBAL;

        $dbh = \Ip\Db::getConnection();
        $sql = '
            SELECT
                value
            FROM
                `'.DB_PREF.'m_inline_value_global`
            WHERE
                `module` = :module AND
                `key` = :key
        ';

        $params = array (
            ':module' => $this->module,
            ':key' => $key
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
        if ($lock = $q->fetch(\PDO::FETCH_ASSOC)) {
            return $lock['value'];
        } else {
            return false;
        }
    }

    // SET
    public function setPageValue($key, $zoneName, $pageId, $value)
    {
        $dbh = \Ip\Db::getConnection();
        $sql = '
            INSERT INTO
                `'.DB_PREF.'m_inline_value_page`
            SET
                `module` = :module,
                `key` = :key,
                `zoneName` = :zoneName,
                `pageId` = :pageId,
                `value` = :value
            ON DUPLICATE KEY UPDATE
                `value` = :value
        ';

        $params = array (
            ':module' => $this->module,
            ':key' => $key,
            ':zoneName' => $zoneName,
            ':pageId' => $pageId,
            ':value' => $value
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);

    }


    public function setLanguageValue($key, $languageId, $value)
    {
        $dbh = \Ip\Db::getConnection();
        $sql = '
            INSERT INTO
                `'.DB_PREF.'m_inline_value_language`
            SET
                `module` = :module,
                `key` = :key,
                `languageId` = :languageId,
                `value` = :value
            ON DUPLICATE KEY UPDATE
                `value` = :value
        ';

        $params = array (
            ':module' => $this->module,
            ':key' => $key,
            ':languageId' => $languageId,
            ':value' => $value
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
    }

    public function setGlobalValue($key, $value)
    {
        $dbh = \Ip\Db::getConnection();
        $sql = '
            INSERT INTO
                `'.DB_PREF.'m_inline_value_global`
            SET
                `module` = :module,
                `key` = :key,
                `value` = :value
            ON DUPLICATE KEY UPDATE
                `value` = :value
        ';

        $params = array (
            ':module' => $this->module,
            ':key' => $key,
            ':value' => $value
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
    }

    // DELETE
    public function deletePageValue($key, $zoneName, $pageId)
    {
        $dbh = \Ip\Db::getConnection();
        $sql = '
            DELETE FROM
                `'.DB_PREF.'m_inline_value_global`
            WHERE
                `module` = :module,
                `key` = :key
                `zoneName` = :zoneName,
                `pageId` = :pageId
        ';

        $params = array (
            ':module' => $this->module,
            ':key' => $key,
            ':zoneName' => $zoneName,
            ':pageId' => $pageId
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
    }

    public function deleteLanguageValue($key, $languageId)
    {
        $dbh = \Ip\Db::getConnection();
        $sql = '
            DELETE FROM
                `'.DB_PREF.'m_inline_value_language`
            WHERE
                `module` = :module,
                `key` = :key,
                `languageId` = :languageId
        ';

        $params = array (
            ':module' => $this->module,
            ':key' => $key,
            ':languageId' => $languageId
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
    }

    public function deleteGlobalValue($key)
    {
        $dbh = \Ip\Db::getConnection();
        $sql = '
            DELETE FROM
                `'.DB_PREF.'m_inline_value_global`
            WHERE
                `module` = :module,
                `key` = :key
        ';

        $params = array (
            ':module' => $this->module,
            ':key' => $key
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
    }


}