<?php


namespace Plugin\Application;


class PublicController extends \Ip\Controller
{
    /**
     * Visit example.com/?pa=Application.hello to view the response
     * Or visit example.com/hello (see Job.php)
     * @return \Ip\View
     */
    public function day($day = null)
    {
        // You can remove this line
        ipAddJs('assets/application.js');
        ipAddCss('assets/application.css');

        if (!$day) {
            $day = date('l');
        }

        $data = array(
            'day' => $day
        );

        //change the layout if you like
        //ipSetLayout('home.php');

        return ipView('view/day.php', $data);
    }
}
