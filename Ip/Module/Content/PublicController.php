<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Module\Content;


class PublicController extends \Ip\Controller
{
    public function index()
    {
        $site = \Ip\ServiceLocator::getSite();
        $response = new \Ip\Response();
        $response->setContent('test');

        if (\Ip\Module\Admin\Service::isSafeMode()) {
            $response->setContent(\Ip\View::create(\Ip\Config::coreModuleFile('Admin/View/safeModeLayout.php'), array())->render());
        } else {
            $layout = \Ip\ServiceLocator::getContent()->getLayout();
            if ($layout) {
                if ($layout[0] == '/') {
                    $viewFile = $layout;
                } else {
                    $viewFile = \Ip\Config::themeFile($layout);
                }
                $response->setContent(\Ip\View::create($viewFile, array())->render());

            }

        }

        return $response;
    }
}