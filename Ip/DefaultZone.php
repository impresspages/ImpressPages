<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip;

use Ip\Element;
use Ip\Frontend\Page;
use Ip\Frontend\Zone;

/**
 *
 * @package ImpressPages
 */
class DefaultZone extends Zone{

    /**
     * Find elements of this zone.
     * @return array Element
     */
    public function getElements($language = null, $parentElementId = null, $startFrom = 1, $limit = null, $includeHidden = false, $reverseOrder = null){
        $answer = array();
        if($parentElementId == null){
            $answer[] = new Page(1, $this->name);
            return $answer;
        }

        return array();
    }


    /**
     * @param int $elementId
     * @return \Ip\Page
     */
    public function getElement($elementId){
        if($elementId == 1){
            return new \Ip\Page(1, $this->name); //default zone return element with all url and get variable combinations
        }
    }


    /**
     * @param array $urlVars
     * @param array $getVars
     * @return Page or false if page does not exist
     */
    public function findElement($urlVars, $getVars){
        return new Page(1, $this->name); //default zone return element with all url and get variable combinations
    }





}


