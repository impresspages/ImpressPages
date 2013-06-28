<?php
    /**
     * @package ImpressPages
     * @copyright   Copyright (C) 2011 ImpressPages LTD.
     *
     */

namespace Modules\developer\inline_management;


class Config
{

    /**
     * @return array
     */
    public function getAvailableFonts()
    {
        global $parametersMod;
        $fontsString = $parametersMod->getValue('developer', 'inline_management', 'options', 'available_fonts');

        $tmpFonts = explode("\n", $fontsString);
        $fonts = array();
        foreach($tmpFonts as &$font) {
            $tmpFont = trim($font);
            if ($tmpFont != '') {
               $fonts[] = $tmpFont;
            }
        }
        return $fonts;
    }
}