<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Upload;

use Ip\Response\JsonRpc;


class AdminController extends \Ip\Controller
{

    public function getImageContainerHtml() {
        $html = ipView('view/imageContainer.php', array())->render();

        $result = array(
            "status" => "success",
            "html" => $html
        );

        // TODO JsonRpc
        return new \Ip\Response\Json($result);
    }




}
