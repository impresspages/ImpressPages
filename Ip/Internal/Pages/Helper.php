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
        'pageVariableName' => 'gpage',
        'filter' => 'parentId = ' . (int) $parentId,
        'fields' => array(
            array(
                'label' => __('Navigation title', 'ipAdmin', FALSE),
                'field' => 'navigationTitle',
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

    public static function menuList()
    {
        $menus = ipDb()->selectAll('page', '`id`, `alias`, `pageTitle`, `languageCode`, `navigationTitle`', array('parentId' => 0));
        foreach($menus as &$menu) {
            $menu['menuType'] = ipPageStorage($menu['id'])->get('menuType', 'tree');
        }
        return $menus;
    }

    public static function menuForm($menuId)
    {
        $menu = ipDb()->selectRow('page', '*', array('id' => $menuId));

        if (!$menu) {
            throw new \Ip\Exception('Menu not found.', array('id' => $menuId));
        }

        $form = new \Ip\Form();

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
                'label' => __('Title (used in admin)', 'ipAdmin', false),
                'value' => $menu['navigationTitle']
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'alias',
                'label' => __('Menu name (used in PHP code)', 'ipAdmin', false),
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
                'label' => __('Layout', 'ipAdmin', false),
                'value' => ipPageStorage($menu['id'])->get('layout', 'main.php'),
                'values' => $values,
            ));
        $form->addField($field);

        $values = array (
            array ('tree', __('Tree (for menu)', 'ipAdmin', FALSE)),
            array ('list', __('List (for blogs)', 'ipAdmin', FALSE)),
        );
        $field = new \Ip\Form\Field\Select(
            array(
                'name' => 'type',
                'label' => __('Type', 'ipAdmin', false),
                'value' => ipPageStorage($menu['id'])->get('menuType', 'main.php'),
                'values' => $values,
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
                'name' => 'urlPath',
                'label' => __('urlPath', 'ipAdmin', false),
                'value' => $page->getUrlPath(),
            ));
        $form->addField($field);


        $field = new \Ip\Form\Field\Checkbox(
            array(
                'name' => 'isVisible',
                'label' => __('Visible', 'ipAdmin', false),
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
                'label' => __('Layout', 'ipAdmin', false),
                'values' => $options,
                'value' => $layout
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'createdAt',
                'label' => __('Created on', 'ipAdmin', false),
                'value' => date('Y-m-d', strtotime($page->getCreatedAt()))
            ));
        $form->addField($field);

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'updatedAt',
                'label' => __('Update on', 'ipAdmin', false),
                'value' => date('Y-m-d', strtotime($page->getUpdatedAt()))
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
                'name' => 'isVisible',
                'label' => __('Visible', 'ipAdmin', false),
                'value' => !ipGetOption('Pages.hideNewPages', 0)
            ));
        $form->addField($field);

        return $form;
    }

    public static function addMenuForm()
    {
        $form = new \Ip\Form();

        $field = new \Ip\Form\Field\Text(
            array(
                'name' => 'title',
                'label' => __('Title', 'ipAdmin', false)
            ));
        $form->addField($field);

        $values = array (
            array ('tree', __('Tree (for menu)', 'ipAdmin', FALSE)),
            array ('list', __('List (for blogs)', 'ipAdmin', FALSE)),
        );
        $field = new \Ip\Form\Field\Select(
            array(
                'name' => 'type',
                'label' => __('Type', 'ipAdmin', false),
                'values' => $values,
            ));
        $form->addField($field);

        return $form;
    }


}
