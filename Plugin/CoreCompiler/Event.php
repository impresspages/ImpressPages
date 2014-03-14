<?php

namespace Plugin\CoreCompiler;


class Event
{
    public static function ipBeforeController()
    {
        $model = new Model();
        $model->generateAdminJS();
        $model->generateIpCoreJS();
        $model->generateManagementJS();
        $model->generateInlineManagementJS();
        $model->generateIpContent();
        $model->generateCoreBootstrap();
    }
}
