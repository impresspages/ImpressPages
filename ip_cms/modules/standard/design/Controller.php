<?php
/**
 * @package ImpressPages
 *
 */

namespace Modules\standard\design;




class Controller extends \Ip\Controller
{


    public function index()
    {
        $this->backendOnly();
        $site = \Ip\ServiceLocator::getSite();

        $data = array(
            'previewUrl' => BASE_URL
        );
        $view = \Ip\View::create('view/layout.php', $data);
        $site->setOutput($view->render());
    }



    protected function backendOnly()
    {
        if (!\Ip\Backend::loggedIn()) {
            throw new \Exception('This controller can be accessed only by administrator');
        }
    }
}