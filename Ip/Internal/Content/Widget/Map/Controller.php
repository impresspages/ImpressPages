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
        if (!$this->gmapsApiAvailable()) {
            if (ipAdminId()) {
                return '<a class="note" href="' . ipActionUrl(array('aa' => 'Config')) . '">' . __('Gmaps can\'t work without a unique key. Please set up Gmaps API key', 'Ip-admin') . '</a>';
            } else {
                //don't show anything on public site
                return '';
            }
        }

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

    private function gmapsApiAvailable()
    {
        return ipGetOption('Config.gmapsApiKey') || ipStorage()->get('Ip', 'upgradedFrom4.6.6', false);
    }



}
