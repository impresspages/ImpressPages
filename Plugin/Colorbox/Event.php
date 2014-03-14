<?php

namespace Plugin\Colorbox;

class Event
{
    public static function ipBeforeController()
    {
        ipAddCss('Plugin/Colorbox/assets/theme1/colorbox.css');
        ipAddJs('Plugin/Colorbox/assets/colorbox/jquery.colorbox-min.js');
        ipAddJs('Plugin/Colorbox/assets/colorboxInit.js');
    }
}
