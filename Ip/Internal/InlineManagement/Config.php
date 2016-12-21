<?php
/**
 * @package ImpressPages
 *
 */

namespace Ip\Internal\InlineManagement;


class Config
{

    /**
     * @return array
     */
    public function getAvailableFonts()
    {
        $fontsString = ipGetOption('Config.availableFonts');

        $tmpFonts = explode("\n", $fontsString);
        $fonts = [];
        foreach ($tmpFonts as &$font) {
            $tmpFont = trim($font);
            if ($tmpFont != '') {
                $fonts[] = $tmpFont;
            }
        }
        return $fonts;
    }
}
