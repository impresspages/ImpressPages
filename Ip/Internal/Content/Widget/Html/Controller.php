<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Content\Widget\Html;




class Controller extends \Ip\WidgetController
{
    public function getTitle() {
        return __('HTML code', 'ipAdmin', false);
    }

    public function adminSnippets()
    {
        $snippets = array();
        $snippets[] = ipView('snippet/edit.php')->render();
        return $snippets;
    }
    
}