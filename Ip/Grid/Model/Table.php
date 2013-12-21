<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Grid\Model;


class Table extends \Ip\Grid\Model
{

    protected $config = null;
    protected $fieldObjects = null;

    public function __construct($config)
    {
        $this->config = $config;

        if (empty($this->config['table'])) {
            throw new \Ip\CoreException('\'table\' configuration value missing.');
        }

        if (empty($this->config['fields'])) {
            $this->config['fields'] = $this->getTableFields($this->config['table']);
        }

        foreach ($this->config['fields'] as &$field) {
            if (empty($field['type'])) {
                $field['type'] = 'Text';
            }
        }
    }

    /**
     * @return \Ip\Grid\Model\Field[]
     */
    protected function getFieldObjects()
    {
        if ($this->fieldObjects === null) {
            $collection = array();
            foreach ($this->config['fields'] as $field) {
                if ($field['type']) {
                    $class = '\Ip\Grid\Model\Field\\' . $field['type'];
                    if (!class_exists($class)) {
                        $class = $field['type']; //type is full class name
                    }
                    $fieldObject = new $class($field);
                    $collection[] = $fieldObject;
                }
            }
            $this->fieldObjects = $collection;
        }
        return $this->fieldObjects;
    }


    public function handleMethod(\Ip\Request $request)
    {
        $data = $request->getRequest();
        if (empty($data['method'])) {
            throw new \Ip\CoreException('Missing request data');
        }
        $method = $data['method'];
        if (!isset($data['params'])) {
            $data['params'] = array();
        }

        switch ($method) {
            case 'init':
                return $this->refresh($data['params']);
                break;
        }

    }


    protected function getTableFields($tableName)
    {
        $sql = "SHOW COLUMNS FROM " . $this->tableName($tableName) . "";

        $fields = ipDb()->fetchColumn($sql);

        $result = array();
        foreach ($fields as $fieldName) {
            $result[] = array(
                'label' => $fieldName,
                'field' => $fieldName
            );
        }

        return $result;
    }

    protected function recordCount()
    {
        return ipDb()->fetchValue("SELECT COUNT(*) FROM " . $this->tableName($this->config['table']) . "");
    }

    protected function fetch($from, $count)
    {
        $sql = "
        SELECT
          *
        FROM
          " . $this->tableName($this->config['table']) . "
        WHERE
          1
        ORDER BY
            `id` DESC
        LIMIT
            $from, $count
        ";

        $result = ipDb()->fetchAll($sql);

        return $result;
    }

    protected function prepareData($data)
    {
        $preparedData = array();
        foreach ($data as $row) {
            $preparedRow = array();
            foreach ($this->getFieldObjects() as $key => $field) {
                $preview = $field->preview($row);
                if (!empty($this->config['fields'][$key]['filter'])) {
                    $filters = $this->config['fields'][$key]['filter'];
                    if (!is_array($filters)) {
                        $filters = array($filters);
                    }
                    foreach ($filters as $filter) {
                        if (substr($filter, 1, 1) !== '\\') {
                            $filter = '\\' . $filter;
                        }
                        $preview = call_user_func($filter, $preview, $row);
                    }
                }
                $preparedRow[] = $preview;
            }
            $preparedData[] = $preparedRow;
        }
        return $preparedData;
    }

    protected function getFieldLabels()
    {
        $labels = array();
        foreach ($this->config['fields'] as $field) {
            $labels[] = $field['label'];
        }
        return $labels;
    }


    protected function refresh($params)
    {
        $currentPage = !empty($params['page']) ? (int)$params['page'] : 1;
        if ($currentPage < 1) {
            $currentPage = 1;
        }

        $pageSize = 5;
        $from = ($currentPage - 1) * $pageSize;
        $totalPages = ceil($this->recordCount() / $pageSize);

        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $pagerSize = 11; // do it odd number (11)
        $pagesLeft = floor($pagerSize / 2) - 2;

        $firstPage = max(1, $currentPage - $pagesLeft);
        if ($firstPage <= 3) {
            $firstPage = 1;
        }

        $pages = array();

        if ($firstPage > 1) {
            $pages = array(1, '..');
        }

        $pages = array_merge($pages, range($firstPage, $currentPage));

        $pagesLeft = max($pagesLeft, $pagerSize - count($pages) - 2);
        $lastPage = min($totalPages, $currentPage + $pagesLeft);

        if ($lastPage + 2 >= $totalPages) {
            $lastPage = $totalPages;
        }

        $pages = array_merge($pages, range($currentPage + 1, $lastPage));

        if ($lastPage < $totalPages) {
            $pages[]= '..';
            $pages[]= $totalPages;
        }

        if (isset($pages[1]) && $pages[1] == '..') {
            $pages[1] = array(
                'text' => '..',
                'page' => floor(($pages[0] + $pages[2]) / 2),
            );
        }

        $beforeLast = count($pages) - 2;
        if (isset($pages[$beforeLast]) && $pages[$beforeLast] == '..') {
            $pages[$beforeLast] = array(
                'text' => '..',
                'page' => floor(($pages[$beforeLast - 1] + $pages[$beforeLast + 1]) / 2),
            );
        }

        $variables = array(
            'labels' => $this->getFieldLabels(),
            'data' => $this->prepareData($this->fetch($from, $pageSize)),
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'pages' => $pages,
        );

        $html = \Ip\View::create('../view/table.php', $variables)->render();
        $commands = array(
            $this->commandSetHtml($html)
        );
        return $commands;
    }

    protected function tableName($tableName)
    {
        return ipTable(str_replace("`", "", $tableName));
    }

}