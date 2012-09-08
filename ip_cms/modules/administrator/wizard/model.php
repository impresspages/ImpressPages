<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\administrator\wizard;



class Model{



    public function disableWizardTip($id)
    {
        global $parametersMod;
        $parametersMod->setValue('administrator', 'wizard', 'options', 'tip_'.$id, false);
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
