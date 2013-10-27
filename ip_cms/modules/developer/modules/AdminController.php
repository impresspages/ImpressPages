<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\developer\modules;
require_once (__DIR__.'/installation.php');

class AdminController extends \Ip\Controller {
    public function install() {
        $installation = new \Modules\developer\modules\ModulesInstallation();
        $errors = $installation->getErrors($_REQUEST['module_group'], $_REQUEST['module']);
        if (!$errors) {
            $installation->recursiveInstall($_REQUEST['module_group'], $_REQUEST['module']);
        } else {
            var_dump ($errors);
        }
    }
}