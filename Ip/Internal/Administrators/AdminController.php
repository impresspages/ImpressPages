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



        ipAddCss(ipFileUrl('Ip/Internal/Administrators/assets/administrators.css'));
        ipAddJs(ipFileUrl('Ip/Internal/Administrators/assets/administrators.js'));

        $data = array (
            'administrators' => $administrators
        );
        return ipView('view/layout.php', $data)->render();

    }
}
