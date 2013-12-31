<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip;


class Slots
{
    protected $slotContent = null;


    public function generateSlot($name, $params = array())
    {
        $content = null;
        $data = array(
            'slotName' => $name,
            'params' => $params
        );

        //dispatch event
        $content = ipDispatcher()->job('site.generateSlot', $data);
        if (!$content) {
            $content = ipDispatcher()->job('site.generateSlot.' . $name, $data);
        }

        if ($content) {
            if (is_object($content) && method_exists($content, 'render')) {
                $content = $content->render();
            }
            return $content;
        }

        //look for predefined content
        $predefinedContent = $this->getSlotContent($name);
        if ($predefinedContent !== null) {
            if (is_object($predefinedContent) && method_exists($predefinedContent, 'render')) {
                $predefinedContent = $content->render();
            }
            return $predefinedContent;
        }

        //execute static slot method
        $parts = explode('.', $name, 2);
        if (count($parts) == 2) {
            if (in_array($parts[0], \Ip\Internal\Plugins\Model::getModules())) {
                $slotClass = 'Ip\\Internal\\' . $parts[0] . '\\Slot';
            } else {
                $slotClass = 'Plugin\\' . $parts[0] . '\\Slot';
            }
            if (method_exists($slotClass, $parts[1])) {
                $content = $slotClass::$parts[1]($params);
                if (is_object($content) && method_exists($content, 'render')) {
                    $content = $content->render();
                }
                return $content;
            }
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
