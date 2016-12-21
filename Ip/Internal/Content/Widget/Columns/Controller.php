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
        return __('Columns', 'Ip-admin', false);
    }



    public function update($widgetId, $postData, $currentData) {

        if (isset($postData['method'])) {
            switch($postData['method']) {
                case 'adjustWidth':
                    if (!isset($postData['widths']) || !is_array($postData['widths'])) {
                        throw new \Ip\Exception("Missing required parameter.");
                    }
                    $currentData['widths'] = $postData['widths'];
                    return $currentData;
                    break;
                case 'addColumn':
                    if (!isset($postData['position'])) {
                        throw new \Ip\Exception("Missing required parameter.");
                    }
                    $currentData = $this->prepareData($currentData, $widgetId);
                    $position = $postData['position'];
                    $i = count($currentData['cols']) + 1;
                    while(in_array('column'.$widgetId.'_' . $i, $currentData['cols'])) {
                        $i++;
                    }
                    $newColumnName = 'column'.$widgetId.'_' . $i;
                    array_splice($currentData['cols'], $position, 0, $newColumnName);

                    $currentData['widths'] = null;
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

                    $currentData['widths'] = null;
                    return $currentData;
                    break;
            }
        } else {
            //return parent::update($widgetId, $postData, $currentData);
            //Do nothing
        }

        return $currentData;

    }

    public function generateHtml($revisionId, $widgetId, $data, $skin)
    {
        $data['revisionId'] = $revisionId;
        $data['widgetId'] = $widgetId;
        $data = $this->prepareData($data, $widgetId);

        return parent::generateHtml($revisionId, $widgetId, $data, $skin);
    }

    public function dataForJs($revisionId, $widgetId, $data, $skin)
    {
        $data = $this->prepareData($data, $widgetId);

        return $data;
    }

    private function prepareData($data, $widgetId)
    {
        if (empty($data['cols']) || !is_array($data['cols'])) {
            $data['cols'] = array(
                'column'.$widgetId.'_1',
                'column'.$widgetId.'_2'
            );
        }

        if (empty($data['widths']) || !is_array($data['widths'])) {
            $data['widths'] = [];
        }

        $totalWidth = (float)0;
        foreach($data['widths'] as $width) {
            $totalWidth = $totalWidth + (float)$width;
        }

        if (count($data['widths']) < count($data['cols']) || $totalWidth > 101 || $totalWidth < 99) {
            $colWidth = 100 / count($data['cols']);
            for($i = 0; $i < count($data['cols']); $i++) {
                $data['widths'][] = $colWidth;
            }
        }


        $data['cols'] = array_values($data['cols']);

        foreach($data['widths'] as &$width) {
            $width = str_replace(',', '.', $width); //in some locales (e.g. PL, 100 / 3 gives comma instead of dot)
        }

        return $data;
    }

    /**
     * Duplicate widget action
     *
     * This function is executed after the widget has been duplicated.
     * All widget data is duplicated automatically. This method is used only in case a widget
     * needs to do some maintenance tasks on duplication.
     *
     * @param int $oldId Old widget ID
     * @param int $newId Duplicated widget ID
     * @param array $data Data that has been duplicated from old widget to the new one
     * @return array new widget data
     */
    public function duplicate($oldId, $newId, $data)
    {
        $data = $this->prepareData($data, $newId);
        $cols = $data['cols'];
        $newCols = [];
        foreach ($cols as $col) {
            $newCols[] = str_replace('column' . $oldId, 'column' . $newId, $col);
        }
        $data['cols'] = $newCols;

        return $data;
    }

}
