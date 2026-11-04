<?php
/**
 * @package ImpressPages
 */

namespace Ip\Internal\Admin;

/**
 * Admin navigation, at most three levels deep:
 *   level 1 - the icon rail (Pages, Design, Settings, Users, Plugins, System),
 *   level 2 - the section column next to it (e.g. Settings > General, Advanced, Languages),
 *   level 3 - lists inside a screen (e.g. the pages of a menu), drawn by the module itself.
 */
class Navigation
{
    /**
     * Level 1 groups and their level 2 entries.
     * An entry is array(module, title, optional '#hash' for screens that share one controller).
     */
    protected static function definition()
    {
        return array(
            'pages' => array(
                'title' => __('Pages', 'Ip-admin', false),
                'icon' => 'fa-sitemap',
                'items' => array(
                    array('Pages', __('Pages', 'Ip-admin', false)),
                ),
            ),
            'design' => array(
                'title' => __('Design', 'Ip-admin', false),
                'icon' => 'fa-pencil',
                'items' => array(
                    array('Design', __('Theme', 'Ip-admin', false)),
                    array('Design', __('Theme options', 'Ip-admin', false), 'options'),
                ),
            ),
            'settings' => array(
                'title' => __('Settings', 'Ip-admin', false),
                'icon' => 'fa-cog',
                'items' => array(
                    array('Config', __('General', 'Ip-admin', false)),
                    array('Config', __('Advanced', 'Ip-admin', false), 'advanced'),
                    array('Languages', __('Languages', 'Ip-admin', false)),
                ),
            ),
            'users' => array(
                'title' => __('Users', 'Ip-admin', false),
                'icon' => 'fa-users',
                'items' => array(
                    array('Administrators', __('Administrators', 'Ip-admin', false)),
                ),
            ),
            'plugins' => array(
                'title' => __('Plugins', 'Ip-admin', false),
                'icon' => 'fa-code-fork',
                'items' => array(
                    array('Plugins', __('Installed plugins', 'Ip-admin', false)),
                ),
            ),
            'system' => array(
                'title' => __('System', 'Ip-admin', false),
                'icon' => 'fa-cogs',
                'items' => array(
                    array('System', __('Overview', 'Ip-admin', false)),
                    array('Log', __('Log', 'Ip-admin', false)),
                    array('Email', __('Email log', 'Ip-admin', false)),
                ),
            ),
        );
    }

    /**
     * Groups the current administrator may see, with resolved URLs and the current group / entry marked.
     *
     * @param string|null $currentModule module of the current request, e.g. 'Config'
     * @return array
     */
    public static function groups($currentModule = null)
    {
        $groups = static::definition();

        // Admin pages of plugins and items that other code adds to the admin menu go under Plugins
        $known = [];
        foreach ($groups as $group) {
            foreach ($group['items'] as $item) {
                $known[] = ipActionUrl(array('aa' => $item[0] . '.index'));
            }
        }
        foreach (Model::instance()->getAdminMenuItems(null) as $menuItem) {
            $url = $menuItem->getUrl();
            if (in_array($url, $known) || strpos($url, 'aa=Content') !== false) {
                continue;
            }
            $groups['plugins']['items'][] = array(null, $menuItem->getTitle(), null, $url);
        }

        $result = [];
        foreach ($groups as $key => $group) {
            $items = [];
            foreach ($group['items'] as $item) {
                $module = $item[0];
                if ($module !== null && !ipAdminPermission($module)) {
                    continue;
                }
                $url = isset($item[3]) ? $item[3] : ipActionUrl(array('aa' => $module . '.index'));
                if (!empty($item[2])) {
                    $url .= '#' . $item[2];
                }
                $items[] = array(
                    'module' => $module,
                    'title' => $item[1],
                    'hash' => isset($item[2]) ? $item[2] : null,
                    'url' => $url,
                    'current' => false,
                );
            }
            if (!$items) {
                continue;
            }

            // the first entry of a module is current unless a #hash entry is chosen in the browser
            $isCurrent = false;
            foreach ($items as &$entry) {
                if (!$isCurrent && $currentModule && $entry['module'] === $currentModule && $entry['hash'] === null) {
                    $entry['current'] = true;
                    $isCurrent = true;
                }
            }
            unset($entry);
            if (!$isCurrent && $currentModule) {
                foreach ($items as &$entry) {
                    $isPluginPage = !$entry['module'] && preg_match('/[?&]aa=' . preg_quote($currentModule, '/') . '(\\.|&|$)/', $entry['url']);
                    if (!$isCurrent && ($entry['module'] === $currentModule || $isPluginPage)) {
                        $entry['current'] = $entry['hash'] === null;
                        $isCurrent = true;
                    }
                }
                unset($entry);
            }

            $result[$key] = array(
                'title' => $group['title'],
                'icon' => $group['icon'],
                'url' => $items[0]['url'],
                'items' => $items,
                'current' => $isCurrent,
            );
        }

        return $result;
    }

    /**
     * Module name of the current admin request ('Config' for aa=Config.index).
     */
    public static function currentModule()
    {
        $aa = (string)ipRequest()->getRequest('aa', '');
        $parts = explode('.', $aa);
        return $parts[0] !== '' ? $parts[0] : null;
    }

    /**
     * The level 1 group of the current request, or null.
     */
    public static function currentGroup(array $groups)
    {
        foreach ($groups as $group) {
            if ($group['current']) {
                return $group;
            }
        }
        return null;
    }
}
