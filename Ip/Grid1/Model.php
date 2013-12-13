<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Grid1;


abstract class Model
{

    public abstract function handleMethod(\Ip\Request $request);

    protected function commandSetHtml($html)
    {
        return array(
            'command' => 'setHtml',
            'html' => $html
        );
    }

}