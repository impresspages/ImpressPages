<?php

namespace Plugin\Kint;


class Event
{
    public static function ipInit()
    {
        require_once __DIR__ . '/Kint/Kint.class.php';
    }
}
