<?php
/**
 * @package ImpressPages

 *
 */

namespace Ip\Internal\InlineValue;


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
    public function getValue($key, $languageId, $pageId)
    {
        //Find value in breadcrumb
        if ($pageId === null) {
            //we can't get breadcrumb if page id is null
            $breadcrumb = array();
        } else {
            $breadcrumb = ipContent()->getBreadcrumb($pageId);
            //var_dump($breadcrumb);exit;
        }
        $breadcrumb = array_reverse($breadcrumb);


        foreach ($breadcrumb as $position => $element) {
            $value = $this->getPageValue($key, $languageId, $element->getId());
            if ($value) {
                if ($position == 0) {
                    $scope = new Entity\Scope();
                    $scope->settype(Entity\Scope::SCOPE_PAGE);
                    $scope->setPageId($element->getId());
                    $scope->setLanguageId($languageId);
                    $this->lastValueScope = $scope;
                } else {
                    $scope = new Entity\Scope();
                    $scope->settype(Entity\Scope::SCOPE_PARENT_PAGE);
                    $scope->setPageId($element->getId());
                    $scope->setLanguageId($languageId);
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


    public function getPageValue($key, $languageId, $pageId)
    {
        $scope = new Entity\Scope();
        $scope->settype(Entity\Scope::SCOPE_PAGE);
        $scope->setPageId($pageId);
        $scope->setLanguageId($languageId);
        $this->lastValueScope = $scope;

        $where = array (
            'plugin' => $this->module,
            'pageId' => $pageId,
            'key' => $key,
        );

        return ipDb()->selectValue('inline_value_page', 'value', $where);
    }

    public function getLanguageValue($key, $languageId)
    {
        $scope = new Entity\Scope();
        $scope->settype(Entity\Scope::SCOPE_LANGUAGE);
        $scope->setLanguageId($languageId);
        $this->lastValueScope = $scope;


        $dbh = ipDb()->getConnection();
        $sql = '
            SELECT
                value
            FROM
                ' . ipTable('inline_value_language') . '
            WHERE
                `plugin` = :module AND
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
        $scope = new Entity\Scope();
        $scope->settype(Entity\Scope::SCOPE_GLOBAL);
        $this->lastValueScope = $scope;

        $dbh = ipDb()->getConnection();
        $sql = '
            SELECT
                value
            FROM
                ' . ipTable('inline_value_global') . '
            WHERE
                `plugin` = :module AND
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
    public function setPageValue($key, $languageId, $pageId, $value)
    {
        $dbh = ipDb()->getConnection();
        $sql = '
            INSERT INTO
                ' . ipTable('inline_value_page') . '
            SET
                `plugin` = :module,
                `key` = :key,
                `pageId` = :pageId,
                `value` = :value
            ON DUPLICATE KEY UPDATE
                `value` = :value
        ';

        $params = array (
            ':module' => $this->module,
            ':key' => $key,
            ':pageId' => $pageId,
            ':value' => $value
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);

    }


    public function setLanguageValue($key, $languageId, $value)
    {
        $dbh = ipDb()->getConnection();
        $sql = '
            INSERT INTO
                ' . ipTable('inline_value_language') . '
            SET
                `plugin` = :module,
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
        $dbh = ipDb()->getConnection();
        $sql = '
            INSERT INTO
                ' . ipTable('inline_value_global') . '
            SET
                `plugin` = :module,
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
    public function deletePageValue($key, $pageId)
    {
        $dbh = ipDb()->getConnection();
        $sql = '
            DELETE FROM
                ' . ipTable('inline_value_page') . '
            WHERE
                `plugin` = :module
                AND `key` = :key
                AND `pageId` = :pageId
        ';

        $params = array (
            ':module' => $this->module,
            ':key' => $key,
            ':pageId' => $pageId
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
    }

    public function deleteLanguageValue($key, $languageId)
    {
        $dbh = ipDb()->getConnection();
        $sql = '
            DELETE FROM
                ' . ipTable('inline_value_language') . '
            WHERE
                `plugin` = :module and
                `key` = :key and
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
        $dbh = ipDb()->getConnection();
        $sql = '
            DELETE FROM
                ' . ipTable('inline_value_global') . '
            WHERE
                `plugin` = :module
                AND `key` = :key
        ';

        $params = array (
            ':module' => $this->module,
            ':key' => $key
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
    }


}
