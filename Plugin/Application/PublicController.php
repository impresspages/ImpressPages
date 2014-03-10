<?php


namespace Plugin\Application;


class PublicController extends \Ip\Controller
{
    /**
     * Visit example.com/?pa=Application.hello to view the response
     * Or visit example.com/hello (see Job.php)
     * @return \Ip\View
     */
    public function page()
    {
        // You can remove this line
        ipAddJs('assets/application.js');
        ipAddCss('assets/application.css');


        $data = array(
            'weekday' => date('l')
        );

        //change the layout if you like
        //ipSetLayout('home.php');

        return ipView('view/hello.php', $data);
    }
}
