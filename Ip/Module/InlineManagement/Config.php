<?php
    /**
     * @package ImpressPages

     *
     */

namespace Ip\Module\InlineManagement;


class Config
{

    /**
     * @return array
     */
    public function getAvailableFonts()
    {
        global $parametersMod;
        $fontsString = $parametersMod->getValue('InlineManagement.available_fonts');

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