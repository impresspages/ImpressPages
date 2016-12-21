<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model\Field;


class Grid extends \Ip\Internal\Grid\Model\Field
{
    protected $config = [];

    /**
     * Create field object for grid
     * @param array $config config of this particular field
     * @param $wholeConfig whole grid setup config
     * @throws \Ip\Exception
     */
    public function __construct($fieldConfig, $wholeConfig)
    {
        $this->parentField = 'id';

        if (empty($fieldConfig['gridId'])) {
            throw new \Ip\Exception('Grid field needs \'gridId\' - a unique identificator of a subgrid.');
        }
        $this->gridId = $fieldConfig['gridId'];

        if (empty($fieldConfig['config'])) {
            throw new \Ip\Exception('Grid field needs \'config\' setting to be set.');
        }

        $this->config = $fieldConfig['config'];

        return parent::__construct($fieldConfig, $wholeConfig);
    }

    public function createField()
    {
        return false;
    }

    public function createData($postData)
    {
        return [];
    }

    public function updateField($curData)
    {
        return false;
    }

    public function updateData($postData)
    {
        return [];
    }


    public function searchField($searchVariables)
    {
        return false;
    }

    public function searchQuery($searchVariables)
    {
        return false;
    }

    /**
     * Generate field value preview for table view. HTML is allowed
     * @param $recordData
     * @internal param array $data current record data
     * @return string
     */
    public function preview($recordData)
    {
        return '<a class="ipsAction" data-method="subgrid" data-params="' . escAttr(json_encode(array('gridParentId' => $recordData[$this->parentField], 'gridId' => $this->gridId))) . '" href="#">' . esc ($this->label) . '</a>';
    }
}
