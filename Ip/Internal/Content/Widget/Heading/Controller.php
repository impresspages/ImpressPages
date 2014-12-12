<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Content\Widget\Heading;

class Controller extends \Ip\WidgetController
{

    public function getTitle()
    {
        return __('Heading', 'Ip-admin', false);
    }


    public function getActionButtons()
    {
        return array(
            array(
                'label' => __('H1', 'Ip-admin'),
                'class' => 'ipsH1'
            ),
            array(
                'label' => __('H2', 'Ip-admin'),
                'class' => 'ipsH2'
            ),
            array(
                'label' => __('H3', 'Ip-admin'),
                'class' => 'ipsH3'
            ),
            array(
                'label' => __('H4', 'Ip-admin'),
                'class' => 'ipsH4'
            ),
            array(
                'label' => __('H5', 'Ip-admin'),
                'class' => 'ipsH5'
            ),
            array(
                'label' => __('H6', 'Ip-admin'),
                'class' => 'ipsH6'
            ),
            array(
                'label' => __('Options', 'Ip-admin'),
                'class' => 'ipsOptions'
            )
        );
    }

    public function adminHtmlSnippet()
    {
        $maxLevel = (int) ipGetOption('Content.widgetHeadingMaxLevel', 6);
        if ($maxLevel > 6) {
            $maxLevel = 6;
        }
        if ($maxLevel < 1) {
            $maxLevel = 1;
        }
        $variables = array(
            'optionsForm' => $this->optionsForm(),
        );
        $variables2 = array(
            'maxLevel' => $maxLevel
        );
        return ipView('snippet/options.php', $variables)->render() . "\n" . ipView('snippet/controls.php', $variables2)->render();
    }

    public function generateHtml($revisionId, $widgetId, $data, $skin)
    {
        $data['showLink'] = false;
        if (!empty($data['link'])) {
            if (!preg_match('/^((http|https):\/\/)/i', $data['link'])) {
                $data['link'] = 'http://' . $data['link'];
            }

            // hiding link in administration
            if (!ipIsManagementState()) {
                $data['showLink'] = true;
            }
        }

        if (empty($data['level']) || (int)$data['level'] < 1) {
            $data['level'] = 1;
        }

        return parent::generateHtml($revisionId, $widgetId, $data, $skin);
    }

    protected function optionsForm()
    {
        $curUrl = \Ip\Internal\UrlHelper::getCurrentUrl();

        $form = new \Ip\Form();
        $form->setEnvironment(\Ip\Form::ENVIRONMENT_ADMIN);


        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'anchor',
                'label' => __('Anchor', 'Ip-admin', false),
                'note' => __('Anchor', 'Ip-admin') . ' <span class="ipsAnchorPreview">' . $curUrl . '#</span>'
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Url(
            array(
                'name' => 'link',
                'label' => __('Link', 'Ip-admin', false),
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'blank',
                'label' => __('Open in new window', 'Ip-admin', false),
            ));
        $form->addField($field);


        return $form; // Output a string with generated HTML form
    }

}
