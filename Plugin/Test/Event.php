<?php

namespace Plugin\Test;


class Event
{
//    public static function ipUrlChanged($data){
//        var_dump($data);exit;
//    }

//    public static function ipCronExecute($info)
//    {
//        var_dump($info);
////        if ($info['firstTimeThisDay'] || $info['test']) {
////            Model::deleteUnusedWidgets();
////        }
//    }


    public static function ipBeforeController()
    {//var_dump( $_SERVER);exit;
        if (ipRequest()->getQuery('testHmvc')) {
            $request = new \Ip\Request();
            $request->setQuery(array('pa' => 'Test.testHmvc'));

            echo ipApplication()->handleRequest($request)->getContent(); exit;

        }
    }

}
