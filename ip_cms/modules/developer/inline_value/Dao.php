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

    const SCOPE_PAGE = 1;
    const SCOPE_PARENT_PAGE = 2;
    const SCOPE_LANGUAGE = 3;
    const SCOPE_GLOBAL = 4;

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

        foreach ($breadcrumb as $key => $element) {
            $value = $this->getPageValue($key, $zoneName, $element->getId());
            if ($value !== false) {
                if ($key == 0) {
                    $this->lastValueScope = self::SCOPE_PAGE;
                } else {
                    $this->lastValueScope = self::SCOPE_PARENT_PAGE;
                }
                return $value;
            }
        }

        //Find language value
        $value = $this->getLanguageValue($key, $languageId);
        if ($value !== false) {
            $this->lastValueScope = self::SCOPE_LANGUAGE;
            return $value;
        }

        //Find global value
        $value = $this->getGlobalValue($key);
        if ($value !== false) {
            $this->lastValueScope = self::SCOPE_GLOBAL;
            return $value;
        }

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
        $this->lastValueScope = self::SCOPE_PAGE;

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
        if (!$q) {
            throw new \Exception($q->errorInfo());
        }
        if ($lock = $q->fetch(\PDO::FETCH_ASSOC)) {
            return $lock['value'];
        } else {
            return false;
        }
    }

    public function getLanguageValue($key, $languageId)
    {
        $this->lastValueScope = self::SCOPE_LANGUAGE;

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
        if (!$q) {
            throw new \Exception($q->errorInfo());
        }
        if ($lock = $q->fetch(\PDO::FETCH_ASSOC)) {
            return $lock['value'];
        } else {
            return false;
        }
    }

    public function getGlobalValue($key)
    {
        $this->lastValueScope = self::SCOPE_GLOBAL;

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
        if (!$q) {
            throw new \Exception($q->errorInfo());
        }

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
            ':zoneName' => $zoneName,
            ':pageId' => $pageId,
            ':value' => $value
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
        if (!$q) {
            throw new \Exception($q->errorInfo());
        }
        if ($lock = $q->fetch(\PDO::FETCH_ASSOC)) {
            return $lock['value'];
        } else {
            return false;
        }
    }


    public function setLanguageValue($key, $languageId, $value)
    {
        $dbh = \Ip\Db::getConnection();
        $sql = '
            INSERT INTO
                `'.DB_PREF.'m_inline_value_global`
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
            ':value' => $value,
            ':languageId' => $languageId
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
        if (!$q) {
            throw new \Exception($q->errorInfo());
        }
        if ($lock = $q->fetch(\PDO::FETCH_ASSOC)) {
            return $lock['value'];
        } else {
            return false;
        }
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
        if (!$q) {
            throw new \Exception($q->errorInfo());
        }
        if ($lock = $q->fetch(\PDO::FETCH_ASSOC)) {
            return $lock['value'];
        } else {
            return false;
        }
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
        if (!$q) {
            throw new \Exception($q->errorInfo());
        }
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
        if (!$q) {
            throw new \Exception($q->errorInfo());
        }
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
        if (!$q) {
            throw new \Exception($q->errorInfo());
        }
    }


}