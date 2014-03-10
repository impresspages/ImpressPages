<?php

namespace Plugin\Test;


class AdminController extends \Ip\Controller
{
    public function index()
    {

    }

    public function getAjaxData()
    {
        $answer = array(
            'name' => 'John',
            'age' => 28
        );

        return new \Ip\Response\Json($answer);
    }
}
