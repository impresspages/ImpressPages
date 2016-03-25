<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Content\Widget\Map;


class Controller extends \Ip\WidgetController
{


    public function getTitle()
    {
        return __('Map', 'Ip-admin', false);
    }


    public function update($widgetId, $postData, $currentData)
    {
        return $postData;
    }


    public function generateHtml($revisionId, $widgetId, $data, $skin)
    {
        if (!empty($data['height'])) {
            $data['height'] = ((int)$data['height']) . 'px';
        } else {
            $data['height'] = '250px';
        }

        if (empty($data['mapTypeId'])) {
            $data['mapTypeId'] = null;
        }
        if (empty($data['zoom'])) {
            $data['zoom'] = null;
        }
        if (empty($data['lat'])) {
            $data['lat'] = null;
        }
        if (empty($data['lng'])) {
            $data['lng'] = null;
        }

        return parent::generateHtml($revisionId, $widgetId, $data, $skin);
    }

    public function adminHtmlSnippet()
    {
        return ipView('snippet/searchbox.php')->render();
    }



}
