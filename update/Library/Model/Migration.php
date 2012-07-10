<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace IpUpdate\Library\Model;


class Migration
{
    private $scripts;

    public function __construct()
    {
    }
    
    public function getScriptsFromVersion($fromVersion){
        $answer = array();
        $currentScript = false;
        while($currentScript = $this->getScriptFromVersion($fromVersion)){
            $answer[] = $currentScript;
            $fromVersion = $currentScript->getDestinationVersion();
        }

        return $answer;
    }

    public function getScriptFromVersion($fromVersion){
        $answer = false;

        foreach ($this->getScripts() as $script) {
            if ($script->getSourceVersion() == $fromVersion){
                $answer = $script;
            }
        }

        return $answer;
    }

    
    private function getScripts()
    {
        if (!$this->scripts) {
            $scripts = array();
            
            $migrationDirListing = scandir(IUL_BASE_DIR.IUL_MIGRATION_DIR);
            foreach ($migrationDirListing as $dir) {
                if ($dir == '.' || $dir == '..') {
                    continue;
                }
                
                if (file_exists(IUL_BASE_DIR.IUL_MIGRATION_DIR.$dir.'/Script.php')) {
                    $scriptName = 'IpUpdate\\Library\\Migration\\'.$dir.'\\Script';
                    $scripts[] = new $scriptName();
                }
            } 
            $this->scripts = $scripts;
        }
        return $this->scripts;
    }


}





