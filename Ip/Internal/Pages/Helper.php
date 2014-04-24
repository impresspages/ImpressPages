<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\Pages;





class Helper
{

    public static function pagesGridConfig($parentId)
    {
        return array(
        'table' => 'page',
        'allowCreate' => FALSE,
        'allowSearch' => FALSE,
        'allowDelete' => FALSE,
        'allowUpdate' => FALSE,
        'sortField' => 'pageOrder',
        'pageSize' => ipGetOption('Pages.pageListSize', 30),
        'pageVariableName' => 'gpage',
        'filter' => 'isDeleted = 0 and parentId = ' . (int) $parentId, //rename to sqlWhere
        'fields' => array(
            array(
                'label' => __('Title', 'Ip-admin', FALSE),
                'field' => 'title',
            ))
        );
    }


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

    public static function menuForm($menuId)
    {
        $menu = Model::getPage((int)$menuId);

        if (!$menu) {
            throw new \Ip\Exception('Menu not found.', array('id' => $menuId));
        }

        $form = new \Ip\Form();
        $form->setEnvironment(\Ip\Form::ENVIRONMENT_ADMIN);

        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'aa',
                'value' => 'Pages.updateMenu'
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Hidden(
            array(
                'name' => 'id',
                'value' => $menu['id']
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'title',
                'label' => __('Title', 'Ip-admin', FALSE),
                'value' => $menu['title']
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'alias',
                'label' => __('Menu name (used in PHP code)', 'Ip-admin', FALSE),
                'value' => $menu['alias']
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
                'label' => __('Layout', 'Ip-admin', FALSE),
                'value' => ipPageStorage($menu['id'])->get('layout', 'main.php'),
                'values' => $values,
            ));
        $form->addField($field);

        $values = array (
            array ('tree', __('Tree (for menu)', 'Ip-admin', FALSE)),
            array ('list', __('List (for blogs)', 'Ip-admin', FALSE)),
        );
        $field = new \Ip\Form\Field\Select(
            array(
                'name' => 'type',
                'label' => __('Type', 'Ip-admin', FALSE),
                'value' => ipPageStorage($menu['id'])->get('menuType', 'main.php'),
                'values' => $values,
            ));
        $form->addField($field);


        return $form;
    }

    public static function pagePropertiesForm($pageId)
    {
        $page = new \Ip\Page($pageId);

        $form = new \Ip\Form();
        $form->setEnvironment(\Ip\Form::ENVIRONMENT_ADMIN);


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
                'name' => 'title',
                'label' => __('Title', 'Ip-admin', FALSE),
                'value' => $page->getTitle()
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'urlPath',
                'label' => __('URL path', 'Ip-admin', FALSE),
                'value' => $page->getUrlPath(),
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'isVisible',
                'label' => __('Visible', 'Ip-admin', FALSE),
                'value' => $page->isVisible()
            ));
        $form->addField($field);


        $layouts = \Ip\Internal\Design\Service::getLayouts();
        $options = array();
        foreach($layouts as $layout) {
            $options[] = array ($layout, $layout);
        }

        $layout = ipPageStorage($pageId)->get('layout', 'main.php');

        $field = new \Ip\Form\Field\Select(
            array(
                'name' => 'layout',
                'label' => __('Layout', 'Ip-admin', FALSE),
                'values' => $options,
                'value' => $layout
            ));
        $form->addField($field);

        $fieldset = new \Ip\Form\Fieldset(__('Seo', 'Ip-admin', FALSE));
        $form->addFieldset($fieldset);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'metaTitle',
                'label' => __('Meta title', 'Ip-admin', FALSE),
                'value' => $page->getMetaTitle()
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'keywords',
                'label' => __('Keywords', 'Ip-admin', FALSE),
                'value' => $page->getKeywords()
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Textarea(
            array(
                'name' => 'description',
                'label' => __('Description', 'Ip-admin', FALSE),
                'value' => $page->getDescription()
            ));
        $form->addField($field);


        $fieldset = new \Ip\Form\Fieldset(__('Other', 'Ip-admin', FALSE));
        $form->addFieldset($fieldset);

        $field = new \Ip\Form\Field\Url(
            array(
                'name' => 'redirectUrl',
                'label' => __('Redirect', 'Ip-admin', FALSE),
                'value' => $page->getRedirectUrl()
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'isDisabled',
                'label' => __('Disabled', 'Ip-admin', FALSE),
                'value' => $page->isDisabled(),
                'note' => 'Won\'t be clickable in menu if selected.'
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'isSecured',
                'label' => __('Secured', 'Ip-admin', FALSE),
                'value' => $page->isSecured(),
                'note' => 'Won\'t be accessible to view even knowing the URL.'
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'isBlank',
                'label' => __('Open in new window', 'Ip-admin', FALSE),
                'value' => $page->isBlank()
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'alias',
                'label' => __('Alias (used in code)', 'Ip-admin', FALSE),
                'value' => $page->getAlias()
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'createdAt',
                'label' => __('Created on', 'Ip-admin', FALSE),
                'value' => date('Y-m-d', strtotime($page->getCreatedAt()))
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'updatedAt',
                'label' => __('Update on', 'Ip-admin', FALSE),
                'value' => date('Y-m-d', strtotime($page->getUpdatedAt()))
            ));
        $form->addField($field);

        $form = ipFilter('ipPagePropertiesForm', $form, array('pageId' => $pageId));

        return $form;
    }

    public static function addPageForm()
    {
        $form = new \Ip\Form();
        $form->setEnvironment(\Ip\Form::ENVIRONMENT_ADMIN);


        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'title',
                'label' => __('Title', 'Ip-admin', FALSE)
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'isVisible',
                'label' => __('Visible', 'Ip-admin', FALSE),
                'value' => !ipGetOption('Pages.hideNewPages', 0)
            ));
        $form->addField($field);

        return $form;
    }

    public static function addMenuForm()
    {
        $form = new \Ip\Form();
        $form->setEnvironment(\Ip\Form::ENVIRONMENT_ADMIN);


        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'title',
                'label' => __('Title', 'Ip-admin', FALSE)
            ));
        $form->addField($field);

        $values = array (
            array ('tree', __('Tree (for menu)', 'Ip-admin', FALSE)),
            array ('list', __('List (for blogs)', 'Ip-admin', FALSE)),
        );
        $field = new \Ip\Form\Field\Select(
            array(
                'name' => 'type',
                'label' => __('Type', 'Ip-admin', FALSE),
                'values' => $values,
            ));
        $form->addField($field);

        return $form;
    }


}
