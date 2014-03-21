<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Content\Widget\Heading;




class Controller extends \Ip\WidgetController{

    public function getTitle() {
        return __('Heading', 'ipAdmin', false);
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

    public function adminHtmlSnippet()
    {
        $variables = array(
            'optionsForm' => $this->optionsForm()
        );
        return ipView('snippet/options.php', $variables)->render() . "\n" . ipView('snippet/controls.php')->render();
    }

    public function generateHtml($revisionId, $widgetId, $instanceId, $data, $skin)
    {
        $data['showLink'] = false;
        if (!empty($data['link']) && !preg_match('/^((http|https):\/\/)/i', $data['link'])) {
            $data['link'] = 'http://' . $data['link'];

            // hiding link in administration
            if (!ipIsManagementState()) {
                $data['showLink'] = true;
            }
        }

        if (empty($data['level']) || (int)$data['level'] < 1) {
            $data['level'] = 1;
        }

        return parent::generateHtml($revisionId, $widgetId, $instanceId, $data, $skin);
    }

    protected function optionsForm()
    {
        $curUrl = \Ip\Internal\UrlHelper::getCurrentUrl();

        $form = new \Ip\Form();
        $form->setEnvironment(\Ip\Form::ENVIRONMENT_ADMIN);


        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'anchor',
                'label' => __('Anchor', 'ipAdmin', false),
                'note' => __('Anchor', 'ipAdmin') .'<span class="ipsAnchorPreview ipmAnchorPreview">'. $curUrl .'#</span>'
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'link',
                'label' => __('Link', 'ipAdmin', false),
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'blank',
                'label' => __('Open in new window', 'ipAdmin', false),
            ));
        $form->addField($field);


        return $form; // Output a string with generated HTML form
    }

}
