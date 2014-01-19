<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Config;




class AdminController extends \Ip\Controller
{

    public function index()
    {


        ipAddJs(ipFileUrl('Ip/Internal/Config/assets/config.js'));

        $form = Forms::getForm();
        $data = array (
            'form' => $form
        );
        return ipView('view/configWindow.php', $data)->render();

    }
}
