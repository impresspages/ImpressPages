<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal;


class Slots
{
    protected $slotContent = null;


    public function generateSlot($name, $params = [])
    {
        $content = \Ip\ServiceLocator::dispatcher()->slot($name, $params);

        //look for predefined content
        $predefinedContent = $this->getSlotContent($name);
        if ($predefinedContent !== null) {
            if (is_object($predefinedContent) && method_exists($predefinedContent, 'render')) {
                $predefinedContent = $content->render();
            }
            return $predefinedContent;
        }


        if ($content) {
            if (is_object($content) && method_exists($content, 'render')) {
                $content = $content->render();
            }
            return $content;
        }


        return '';
    }

    public function getSlotContent($name)
    {
        if (isset($this->slotContent[$name])) {
            return $this->slotContent[$name];
        } else {
            return null;
        }
    }


    public function setSlotContent($name, $content)
    {
        $this->slotContent[$name] = $content;
    }


}
