<?php
/**
 * @package   ImpressPages
 */

namespace Plugin\HelloWorld;


class SiteController extends \Ip\Controller
{
    public function pages($language = null)
    {
        $pages = \Plugin\SimplePage\ControllerZone::bindPages(
            array(
                '' => array( // maybe '/' ?
                    'id' => 1,
                    'title' => 'Hello World',
                    'action' => 'indexPage',
                ),
                'greetings' => array(
                    'id' => 2,
                    'title' => 'Greetings!',
                    'action' => 'greetingsPage',
                ),
                'greetings/friend' => array(
                    'id' => 3,
                    'title' => 'Greetings friend!',
                    'action' => 'greetingsFriendPage',
                ),
            )
        );

        return $pages;
    }

    public function indexPage()
    {
        // get current zone element?
        return 'Hello World';
    }

    public function greetingsPage($name = 'stranger')
    {
        $data = array(
            'name' => $name
        );

        return \Ip\View::create('view/greetings.php', $data)->render();
    }

    public function greetingsFriendPage()
    {
        return $this->greetingsPage('friend');
    }
}