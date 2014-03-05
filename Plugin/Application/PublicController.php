<?php


namespace Plugin\Application;


class PublicController extends \Ip\Controller
{
    public function hello()
    {
        // You can remove this line
        ipAddJs('assets/application.js');

        $data = array(
            'weekday' => date('l')
        );

        return ipView('view/hello.php', $data);
    }
}
