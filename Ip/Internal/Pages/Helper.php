<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Pages;





class Helper
{

    public static function languageList()
    {
        $answer = array();
        $languages = ipContent()->getLanguages();
        foreach($languages as $language)
        {
            $answer[] = array(
                'id' => $language->getId(),
                'title' => $language->getTitle(),
                'abbreviation' => $language->getAbbreviation(),
                'code' => $language->getCode(),
            );
        }
        return $answer;
    }

    public static function menuList()
    {
        return ipDb()->selectAll('page', '`id`, `alias`, `pageTitle`, `navigationTitle`', array('parentId' => 0));
    }

    public static function zoneForm($zoneName)
    {


        $zone = ipContent()->getZone($zoneName);
        if (!$zone) {
            throw new \Ip\Exception('Unknown zone: ' . $zoneName);
        }

        $form = new \Ip\Form();

        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'aa',
                'value' => 'Pages.updateZone'
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'zoneName',
                'value' => $zoneName
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'title',
                'label' => __('Title (used in admin)', 'ipAdmin', false),
                'value' => $zone->getTitleInAdmin()
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'url',
                'label' => __('URL', 'ipAdmin', false),
                'value' => $zone->getUrl()
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'name',
                'label' => __('Name (used as ID in PHP code)', 'ipAdmin', false),
                'value' => $zone->getName()
            ));
        $form->addField($field);


        $layouts = \Ip\Internal\Design\Service::getLayouts();
        $values = array();
        foreach ($layouts as $layout) {
            $values[] = array($layout, $layout);
        }

        $field = new \Ip\Form\Field\Select(
            array(
                'name' => 'layout',
                'label' => __('Layout', 'ipAdmin', false),
                'value' => $zone->getLayout(),
                'values' => $values,
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'metaTitle',
                'label' => __('Meta title', 'ipAdmin', false),
                'value' => $zone->getTitle()
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'metaKeywords',
                'label' => __('Meta keywords', 'ipAdmin', false),
                'value' => $zone->getKeywords()
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Textarea(
            array(
                'name' => 'metaDescription',
                'label' => __('Meta description', 'ipAdmin', false),
                'value' => $zone->getDescription()
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Submit(
            array(
                'name' => 'submit',
                'value' => __('Save', 'ipAdmin', false)
            ));
        $form->addField($field);


        return $form;


    }

    public static function pagePropertiesForm($pageId)
    {
        $page = new \Ip\Page($pageId);

        $form = new \Ip\Form();

        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'aa',
                'value' => 'Pages.updatePage'
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'pageId',
                'value' => $pageId
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'navigationTitle',
                'label' => __('Navigation title', 'ipAdmin', false),
                'value' => $page->getNavigationTitle()
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'pageTitle',
                'label' => __('Page title', 'ipAdmin', false),
                'value' => $page->getPageTitle()
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'keywords',
                'label' => __('Keywords', 'ipAdmin', false),
                'value' => $page->getKeywords()
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Textarea(
            array(
                'name' => 'description',
                'label' => __('Description', 'ipAdmin', false),
                'value' => $page->getDescription()
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'slug',
                'label' => __('Url slug', 'ipAdmin', false),
                'value' => $page->getSlug(),
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'visible',
                'label' => __('Visible', 'ipAdmin', false),
                'value' => 1,
                'value' => $page->isVisible()
            ));
        $form->addField($field);


        $layouts = \Ip\Internal\Design\Service::getLayouts();
        $options = array();
        foreach($layouts as $layout) {
            $options[] = array ($layout, $layout);
        }

        // TODOX layout
//        $curLayout = \Ip\Internal\ContentDb::getPageLayout(
//            $zone->getAssociatedModule(),
//            $page->getId()
//        );
//        if (!$curLayout) {
//            $curLayout = $zone->getLayout();
//        }
//        $field = new \Ip\Form\Field\Select(
//            array(
//                'name' => 'layout',
//                'label' => __('Layout', 'ipAdmin', false),
//                'values' => $options,
//                'value' => $curLayout
//            ));
//        $form->addField($field);


        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'createdOn',
                'label' => __('Created on', 'ipAdmin', false),
                'value' => date('Y-m-d', strtotime($page->getCreatedOn()))
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'lastModified',
                'label' => __('Update on', 'ipAdmin', false),
                'value' => date('Y-m-d', strtotime($page->getLastModified()))
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Submit(
            array(
                'name' => 'submit',
                'value' => __('Save', 'ipAdmin', false)
            ));
        $form->addField($field);

        return $form;
    }

    public static function addPageForm()
    {
        $form = new \Ip\Form();

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'title',
                'label' => __('Title', 'ipAdmin', false)
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'visible',
                'label' => __('Visible', 'ipAdmin', false),
                'value' => ipGetOption('Pages.hideNewPages', 1)
            ));
        $form->addField($field);

        return $form;
    }

    public static function addZoneForm()
    {
        $form = new \Ip\Form();

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'title',
                'label' => __('Title', 'ipAdmin', false)
            ));
        $form->addField($field);

        return $form;
    }


}
