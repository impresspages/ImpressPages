<?php
/**
 * @package   ImpressPages
 */


/**
 * Created by PhpStorm.
 * User: maskas
 * Date: 16.3.6
 * Time: 21.40
 */

namespace Ip\Internal;


class ModuleHelper
{
    public static function getModules()
    {
        $modules = array(
            "Core",
            "Content",
            "Admin",
            "Pages",
            "Administrators",
            "Design",
            "Plugins",
            "Log",
            "Email",
            "Config",
            "Breadcrumb",
            "Repository",
            "InlineManagement",
            "Languages",
            "Cron",
            "Translations",
            "System",
            "Update",
            "Ecommerce"
        );


        /**
         * Introduce ipModulesFilter function in index.php file to add or remove system modules. Useful if you build MultiSite and other special cases.
         */
        if (function_exists('ipModulesFilter')) {
            $modules = ipModulesFilter($modules);
        }

        return $modules;
    }
}
