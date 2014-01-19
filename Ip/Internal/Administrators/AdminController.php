<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Administrators;




class AdminController extends \Ip\Controller
{

    public function index()
    {

        $administrators = Model::getAll();



        ipAddJs(ipFileUrl('Ip/Internal/Config/assets/config.js'));

        $data = array (
            'administrators' => $administrators
        );
        return ipView('view/layout.php', $data)->render();

    }
}
