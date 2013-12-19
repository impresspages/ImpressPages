<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Wizard;



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
