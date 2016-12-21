<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model;

/**
 * Table helper class designated to prepare data for display
 * @package Ip\Internal\Grid\Model
 */
class Db
{

    /**
     * @var Config
     */
    protected $config = null;

    protected $statusVariables = null;

    protected $defaultLanguageCode = null;

    public function __construct(Config $config, $statusVariables)
    {
        $this->config = $config;
        $this->statusVariables = $statusVariables;
    }

    public function buildSqlWhere()
    {
        $where = $this->config->filter();
        $depth = Status::depth($this->statusVariables);
        if ($depth > 1) {
            if (!$this->config->connectionField()) {
                throw new \Ip\Exception("Nested GRID require 'connectionField' setting to be set.");
            }
            $where .= ' and (' . $where . ') and ' . $this->config->tableName() . '.`' . $this->config->connectionField() . '` = ' . ipDb()->getConnection()->quote($this->statusVariables['gridParentId' . ($depth - 1)]);
        }

        $searchVariables = [];
        foreach ($this->statusVariables as $key => $value) {
            if (preg_match('/^s_/', $key)) {
                $searchVariables[substr($key, 2)] = $value;
            }
        }

        if (!empty($searchVariables)) {

            foreach ($this->config->fields() as $fieldData) {
                if (!empty($fieldData['type']) && $fieldData['type'] == 'Tab') {
                    continue;
                }
                $fieldObject = $this->config->fieldObject($fieldData);
                $fieldQuery = $fieldObject->searchQuery($searchVariables);
                if ($fieldQuery) {
                    if ($where != ' ') {
                        $where .= ' and ';
                    }
                    $where .= $fieldQuery;
                }
            }
        }


        return $where;
    }

    public function breadcrumbTitle($depth)
    {
        if ($depth == 0) {
            return $this->config->getTitle();
        }
        $subgridConfig = $this->config->subgridConfig($this->statusVariables, $depth);
        $id = $this->statusVariables['gridParentId' . $depth];

        $title = ipDb()->fetchValue(
            "SELECT " . $subgridConfig->tableName() . ".`" . $subgridConfig->getBreadcrumbField() . "` FROM " . $subgridConfig->tableName() . " " . $this->joinQuery(
            ) . " WHERE " . $subgridConfig->tableName() . '.`' . $subgridConfig->idField() . '` = ' . ipDb()->getConnection()->quote($id) . " "
        );
        return $title;
    }

    /**
     * Set default language for multilingual fields
     * Meaningful only in multilingual mode
     * @param $languageCode
     * @return null|string
     */
    public function setDefaultLanguageCode($languageCode)
    {
        $curCode = $this->getDefaultLanguageCode();
        $this->defaultLanguageCode = $languageCode;
        return $curCode;
    }

    /**
     * Meaningful only in multilingual mode
     * @return null|string
     */
    public function getDefaultLanguageCode()
    {
        if ($this->defaultLanguageCode == null) {
            if (!empty($this->statusVariables['language'])) {
                //language selected by the user
                $this->defaultLanguageCode = $this->statusVariables['language'];
            } else {
                //first language
                $languages = ipContent()->getLanguages();
                $firstLanguage = $languages[0];
                $this->defaultLanguageCode = $firstLanguage->getCode();
            }
        }
        return $this->defaultLanguageCode;
    }

    public function joinQuery($languageCode = null)
    {
        $joinQuery = false;
        if ($languageCode == null) {
            $languageCode = $this->getDefaultLanguageCode();
        }

        if ($this->config->isMultilingual()) {
            // join language table
            $languageTable = $this->config->languageTableName();
            $idField = $this->config->tableName() . '.`' . $this->config->idField() . '`';
            $languageReferenceField = $languageTable . '.`' . $this->config->languageForeignKeyField() . '`';
            $languageCodeField =  $languageTable . '.`' . $this->config->languageCodeField() . '`';
            $joinQuery .= " LEFT OUTER JOIN $languageTable ON $idField = $languageReferenceField AND $languageCodeField = " . ipDb()->getConnection()->quote($languageCode) . "";
        }

        if ($this->config->joinQuery()) {
            if ($joinQuery != '') {
                $joinQuery .= ' ';
            }
            $joinQuery .= $this->config->joinQuery();
        }
        return $joinQuery;
    }

    public function recordCount($where)
    {
        $sql = "SELECT COUNT(*) FROM " . $this->config->tableName() . " " . $this->joinQuery() . " WHERE " . $where . " ";
        return ipDb()->fetchValue($sql);
    }

    public function fetch($from, $count, $where = 1)
    {


        $sql = "
        SELECT
          " . $this->config->selectFields() . "
        FROM
          " . $this->config->tableName() . "
          " . $this->joinQuery() . "
        WHERE
          " . $where . "
        ORDER BY
            " . $this->config->orderBy($this->statusVariables) . "
        LIMIT
            $from, $count
        ";

        $result = ipDb()->fetchAll($sql);

        return $result;
    }


    public function fetchRow($id)
    {
        $sql = "
        SELECT
          " . $this->config->selectFields() . "
        FROM
          " . $this->config->tableName() . "
          " . $this->joinQuery() . "
        WHERE
          " . $this->config->tableName() . ".`" . $this->config->idField() . "` = :id
        ";

        $params = array(
            'id' => $id
        );

        $result = ipDb()->fetchRow($sql, $params);

        return $result;
    }

}
