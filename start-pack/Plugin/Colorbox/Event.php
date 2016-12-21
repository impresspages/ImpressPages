<?php

namespace Plugin\Colorbox;

class Event
{
    public static function ipBeforeController()
    {
        $style = ipGetOption('Colorbox.style', 1);
        ipAddCss('Plugin/Colorbox/assets/theme' . $style . '/colorbox.css');
        ipAddJs('Plugin/Colorbox/assets/colorbox/jquery.colorbox-min.js');
        ipAddJs('Plugin/Colorbox/assets/colorboxInit.js');
    }
}
