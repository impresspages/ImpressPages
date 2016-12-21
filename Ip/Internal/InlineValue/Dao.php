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

    /**
     * Get value
     *
     * @param string $key
     * @param int $languageId
     * @param int $pageId
     * @return Entity\Scope
     */
    public function getValue($key, $languageId, $pageId)
    {
        // Find value in breadcrumb.
        if ($pageId === null) {
            // We can't get breadcrumb if page id is null.
            $breadcrumb = [];
        } else {
            $breadcrumb = ipContent()->getBreadcrumb($pageId);
            // var_dump($breadcrumb);
            // exit;
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

        // Find language value.
        $value = $this->getLanguageValue($key, $languageId);
        if ($value !== false) {
            $scope = new Entity\Scope();
            $scope->settype(Entity\Scope::SCOPE_LANGUAGE);
            $scope->setLanguageId($languageId);
            $this->lastValueScope = $scope;
            return $value;
        }

        // Find global value.
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
     * Get page value
     *
     * @param string $key
     * @param int $languageId
     * @param int $pageId
     * @return mixed|null
     */
    public function getPageValue($key, $languageId, $pageId)
    {
        $scope = new Entity\Scope();
        $scope->settype(Entity\Scope::SCOPE_PAGE);
        $scope->setPageId($pageId);
        $scope->setLanguageId($languageId);
        $this->lastValueScope = $scope;

        $where = array(
            'plugin' => $this->module,
            'pageId' => $pageId,
            'key' => $key,
        );

        return ipDb()->selectValue('inline_value_page', 'value', $where);
    }

    /**
     * Get language value
     *
     * @param string $key
     * @param int $languageId
     * @return bool
     */
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

        $params = array(
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

    /**
     * Get global value
     *
     * @param string $key
     * @return bool
     */
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

        $params = array(
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

    /**
     * Get last operation scope
     *
     * @return int
     */
    public function getLastOperationScope()
    {
        return $this->lastValueScope;
    }

    /**
     * Set page value
     *
     * @param string $key
     * @param int $languageId
     * @param int $pageId
     * @param string $value
     */
    public function setPageValue($key, $languageId, $pageId, $value)
    {
        $keys = array(
            'plugin' => $this->module,
            'key' => $key,
            'pageId' => $pageId
        );
        $values = array(
            'value' => $value
        );
        ipDb()->upsert('inline_value_page', $keys, $values);
    }

    /**
     * Set language value
     *
     * @param string $key
     * @param int $languageId
     * @param string $value
     */
    public function setLanguageValue($key, $languageId, $value)
    {
        $keys = array(
            'plugin' => $this->module,
            'key' => $key,
            'languageId' => $languageId
        );
        $values = array(
            'value' => $value
        );
        ipDb()->upsert('inline_value_language', $keys, $values);
    }

    /**
     * Get global value
     *
     * @param string $key
     * @param string $value
     */
    public function setGlobalValue($key, $value)
    {
        $keys = array(
            'plugin' => $this->module,
            'key' => $key
        );
        $values = array(
            'value' => $value
        );
        ipDb()->upsert('inline_value_global', $keys, $values);
    }

    /**
     * Delete page value
     *
     * @param string $key
     * @param int $pageId
     */
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

        $params = array(
            ':module' => $this->module,
            ':key' => $key,
            ':pageId' => $pageId
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
    }

    /**
     * Delete language value
     *
     * @param string $key
     * @param int $languageId
     */
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

        $params = array(
            ':module' => $this->module,
            ':key' => $key,
            ':languageId' => $languageId
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
    }

    /**
     * Delete global value
     *
     * @param string $key
     */
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

        $params = array(
            ':module' => $this->module,
            ':key' => $key
        );
        $q = $dbh->prepare($sql);
        $q->execute($params);
    }

}
