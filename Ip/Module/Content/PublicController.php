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
        $response = new \Ip\Response();


        if (
            \Ip\ServiceLocator::getContent()->getLanguageUrl() != ipGetCurrentlanguage()->getUrl() ||
            ipGetCurrentPage() instanceof \Ip\Frontend\Page404
        ) {
            //TODOX output some content alongside the header
            return new \Ip\Response\Status404();
        }

        echo get_class(ipGetCurrentPage());exit;

        if (ipGetCurrentPage()) {
            ipSetBlockContent('main', ipGetCurrentPage()->generateContent());
        }

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