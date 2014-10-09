<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Pages;


class Event
{
    public static function ipUrlChanged($info)
    {
        $httpExpression = '/^((http|https):\/\/)/i';
        if (!preg_match($httpExpression, $info['oldUrl'])) {
            return;
        }
        if (!preg_match($httpExpression, $info['newUrl'])) {
            return;
        }
        Model::updateUrl($info['oldUrl'], $info['newUrl']);
    }

    public static function ipLanguageAdded($data)
    {
        $languageId = $data['id'];
        $language = ipContent()->getLanguage($languageId);

        $allLanguages = ipContent()->getLanguages();
        $firstLanguage = $allLanguages[0];

        $menus = Service::getMenus($firstLanguage->getCode());

        foreach ($menus as $menu) {
            $menuId = Service::createMenu($language->getCode(), $menu['alias'], $menu['title']);
            Service::updateMenu($menuId, $menu['alias'], $menu['title'], $menu['layout'], $menu['type']);
        }
    }

    public static function ipBeforeLanguageDeleted($data)
    {
        $languageId = $data['id'];
        $language = ipContent()->getLanguage($languageId);
        $menus = Service::getMenus($language->getCode());

        foreach ($menus as $menu) {
            Service::deletePage($menu['id']);
        }
    }
}
