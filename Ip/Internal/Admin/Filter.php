<?php


namespace Ip\Internal\Admin;


class Filter
{
    public static function ipRequestedPage($requestedPage, $info)
    {
        // @var $request \Ip\Request
        $request = $info['request'];
        $relativePath = $request->getRelativePath();

        if (in_array($relativePath, array('admin', 'admin/', 'admin.php', 'admin.php/'))) {
            $requestedPage['controllerModule'] = 'Admin';
            $requestedPage['controllerType'] = \Ip\Request::CONTROLLER_TYPE_SITE;
            $requestedPage['controllerClass'] = '\\Ip\\Internal\\Admin\\SiteController';
            $requestedPage['controllerAction'] = 'login';
        }

        return $requestedPage;
    }
}