<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\standard\design;


class System{


    public function init(){
        $site = \Ip\ServiceLocator::getSite();

        if (isset($_GET['ipDesignPreview'])) {
            $site->addJavascript(BASE_URL.MODULE_DIR.'standard/design/public/customization.js');
        }


    }

}


