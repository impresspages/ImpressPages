<?php

namespace Plugin\CoreCompiler;


class Event
{
    public static function ipInit()
    {
        $model = new Model();
        $model->generateIpCoreJS();
        $model->generateManagementJS();
        $model->generateInlineManagementJS();
        $model->generateIpContent();
        $model->generateCoreBootstrap();
    }
}
