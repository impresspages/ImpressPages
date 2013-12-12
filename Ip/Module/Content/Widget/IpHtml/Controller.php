<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Content\Widget\IpHtml;




class Controller extends \Ip\WidgetController
{
    public function getTitle() {
        return __('HTML code', 'ipAdmin', false);
    }

    public function adminSnippets()
    {
        $snippets = array();
        $snippets[] = \Ip\View::create('snippet/edit.php')->render();
        return $snippets;
    }
    
}