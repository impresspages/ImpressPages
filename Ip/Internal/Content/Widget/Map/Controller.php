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
        if (!empty($data['width'])) {
            $data['width'] = ((int)$data['width']) . 'px';
        } else {
            $data['width'] = '100%';
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

    protected function settingsForm()
    {
        $form = new \Ip\Form();
        $form->setEnvironment(\Ip\Form::ENVIRONMENT_ADMIN);


        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'title',
                'label' => __('Title', 'Ip-admin', false),
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Textarea(
            array(
                'name' => 'description',
                'label' => __('Description', 'Ip-admin', false),
            ));
        $form->addField($field);


        return $form; // Output a string with generated HTML form
    }


}
