<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Content\Widget\Columns;


class Controller extends \Ip\WidgetController
{

    public function getTitle()
    {
        return __('Columns', 'ipAdmin', false);
    }



    public function update($widgetId, $postData, $currentData) {

        if (isset($postData['method'])) {
            switch($postData['method']) {
                case 'addColumn':
                    if (!isset($postData['position'])) {
                        throw new \Ip\Exception("Missing required parameter.");
                    }
                    $currentData = $this->prepareData($currentData, $widgetId);
                    $position = $postData['position'];
                    $newColumnName = 'column'.$widgetId.'_' . (count($currentData['cols']) + 1);
                    array_splice($currentData['cols'], $position, 0, $newColumnName);
                    return $currentData;

                break;
                case 'deleteColumn':
                    if (!isset($postData['columnName'])) {
                        throw new \Ip\Exception("Missing required parameter.");
                    }
                    $currentData = $this->prepareData($currentData, $widgetId);

                    if (is_array($postData['columnName'])) {
                        foreach($postData['columnName'] as $colName) {
                            $index = array_search($colName,$currentData['cols']);
                            if ($index !== FALSE) {
                                unset($currentData['cols'][$index]);
                            }
                        }
                    } else {
                        $index = array_search($postData['columnName'],$currentData['cols']);
                        if ($index !== FALSE) {
                            unset($currentData['cols'][$index]);
                        }
                    }

                    return $currentData;
                    break;
            }
        } else {
            return parent::update($widgetId, $postData, $currentData);
        }

    }

    public function generateHtml($revisionId, $widgetId, $instanceId, $data, $layout)
    {
        $data['revisionId'] = $revisionId;
        $data['widgetId'] = $widgetId;
        $data = $this->prepareData($data, $widgetId);

        return parent::generateHtml($revisionId, $widgetId, $instanceId, $data, $layout);
    }

    public function dataForJs($revisionId, $widgetId, $instanceId, $data, $layout)
    {
        $data = $this->prepareData($data, $widgetId);

        return $data;
    }

    private function prepareData($data, $widgetId)
    {
        if (empty($data['cols'])) {
            $data['cols'] = array(
                'column'.$widgetId.'_1',
                'column'.$widgetId.'_2'
            );
        }

        return $data;
    }


}