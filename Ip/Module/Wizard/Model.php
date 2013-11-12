<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Wizard;



class Model{



    public function disableWizardTip($id)
    {
        ipSetOption('Wizard.tip_'.$id, false);
    }

    public function getTipIds()
    {
        return array(
            'dragWidget',
            'dropWidget',
            'changeWidgetContent',
            'confirmWidget',
            'publish'
        );
    }

}
