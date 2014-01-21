<?php

namespace Plugin\CoreCompiler;


class Event
{
    public static function ipInit()
    {
        /**
         * 1. define assets to compile: LESS, JS
         * 2. define the where goes output
         * 3. load required classes for each process
         * 4. check if action needed
         * 5. execute
         */

        $model = new Model();
        $model->generateManagementJS();
        $model->generateInlineManagementJS();
        $model->generateIpContent();
        $model->generateCoreBootstrap();
    }
}
