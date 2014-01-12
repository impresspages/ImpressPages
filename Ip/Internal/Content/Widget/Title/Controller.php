<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Content\Widget\Title;




class Controller extends \Ip\WidgetController{

    public function getTitle() {
        return __('Title', 'ipAdmin', false);
    }


    public function getActionButtons()
    {
        return array(
            array (
                'label' => __('H1', 'ipAdmin'),
                'class' => 'ipsH1'
            ),
            array (
                'label' => __('H2', 'ipAdmin'),
                'class' => 'ipsH2'
            ),
            array (
                'label' => __('H3', 'ipAdmin'),
                'class' => 'ipsH3'
            ),
            array (
                'label' => __('Options', 'ipAdmin'),
                'class' => 'ipsOptions'
            )
        );
    }

    public function adminSnippets()
    {
        $snippets[] = ipView('snippet/controls.php')->render();
        $variables = array(
            'curUrl' => \Ip\Internal\UrlHelper::getCurrentUrl()
        );
        $snippets[] = ipView('snippet/options.php', $variables)->render();
        return $snippets;
    }

    public function generateHtml($revisionId, $widgetId, $instanceId, $data, $layout)
    {
        if (empty($data['level']) || (int)$data['level'] < 1) {
            $data['level'] = 1;
        }
        return parent::generateHtml($revisionId, $widgetId, $instanceId, $data, $layout);
    }

}