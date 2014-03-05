<?php

namespace Plugin\Kint;


class Event
{
    public static function ipInitFinished()
    {
        require_once __DIR__ . '/Kint/Kint.class.php';
    }
}
