<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Grid\Model;


abstract class Field
{
    /**
     * Generate field value preview for table view. HTML is allowed
     * @param array() $data current record data
     * @return string
     */
    public abstract function preview($data);

    /**
     * Return an object which can be used as a field for standard Ip\Form class.
     * @return \Ip\Form\Field
     */
    public abstract function createField();

    /**
     * Grid doesn't put user's input directly into the database. Each field type decides how to process
     * submitted data. Use this method to process submitted data and return associative array of values to be
     * stored to the database. If you need to do some other actions on other tables or process files after new
     * record has been created, use onCreate method.
     * @param $postData user posted data
     * @return array
     */
    public abstract function createData($postData);

    /**
     * Return an object which can be used as a field for standard Ip\Form class.
     * @param $curData current record data
     * @return \Ip\Form\Field
     */
    public abstract function updateField($curData);

    /**
     * Grid doesn't put user's input directly into the database. Each field type decides how to process
     * submitted data. Use this method to process submitted data and return associative array of values to be
     * stored to the database. If you need to do some other actions on other tables or process files after update, use onUpdate method.
     * @param $postData user posted data
     * @return array
     */
    public abstract function updateData($postData);

    /**
     * Return an object which can be used as a field for standard Ip\Form class.
     * @param array $searchVariables current search filter values
     * @return \Ip\Form\Field
     */
    public abstract function searchField($searchVariables);

    /**
     * Process entered search values and provide part of SQL query which can be added in WHERE clause.
     * @param array $searchVariables user's posted search values
     * @return string
     */
    public abstract function searchQuery($searchVariables);

    /**
     * Executed after new record has been created
     * @param int $recordId
     * @param array $curData
     */
    public function onCreate($recordId, $curData)
    {
        //do nothing by default
    }

    /**
     * Executed after update
     * @param int $recordId
     * @param array $oldData
     * @param array $newData
     */
    public function onUpdate($recordId, $oldData, $newData)
    {
        //do nothing by default
    }

    /**
     * Executed before deleting the record
     * @param int $recordId
     * @param array $curData
     */
    public function onDelete($recordId, $curData)
    {
        //do nothing by default
    }

}
