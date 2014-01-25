<?php
/**
 * @package ImpressPages

 *
 */

namespace IpUpdate\Library;


/**
 * Class Service
 * Service to execute update
 * @package IpUpdate\Library
 */
class Service
{
    private $cf;
    private $installationDir;
    private $parameters;

    public function __construct($installationDir)
    {
        $this->installationDir = $installationDir;
        $configurationParser = new \IpUpdate\Library\Model\ConfigurationParser();
        $this->cf = $configurationParser->parse($installationDir);
        $this->parameters = new \IpUpdate\Library\Model\ConfigurationParser($this->cf['BASE_DIR'] . 'file/tmp/update/');
    }


    /**
     * Start or proceed update process.
     * @param string $step
     * @param \IpUpdate\Library\Options $options
     * @throws \IpUpdate\Library\UpdateException
     */
    public function proceed($step = \IpUpdate\Library\Model\Update::STEP_FINISH, \IpUpdate\Library\Options $options = null)
    {
        $update = new \IpUpdate\Library\Model\Update($this->cf);
        $update->proceed($step, $options);
    }
    
    /**
     * @throws \IpUpdate\Library\UpdateException
     */
    public function resetLock()
    {
        $update = new \IpUpdate\Library\Model\Update($this->cf);
        $update->resetLock();
    }
    
    public function rollback()
    {
    }
    
    public function getCurrentVersion()
    {
        $update = new \IpUpdate\Library\Model\Update($this->cf);
        return $update->getCurrentVersion();
    }

    public function getDestinationVersion()
    {
        $updateModel = new \IpUpdate\Library\Model\Migration();
        $destinationScript = $updateModel->getDestinationScript($this->getCurrentVersion());
        if (!$destinationScript) {
            return false;
        } else {
            return $destinationScript->getDestinationVersion();
        }
    }

    
    public function getUpdateNotes()
    {
        $updateModel = new \IpUpdate\Library\Model\Migration();
        $scripts = $updateModel->getScriptsFromVersion($this->getCurrentVersion());
        $notes = array();
        foreach ($scripts as $script) {
            $newNotes = $script->getNotes($this->cf);
            $notes = array_merge($notes, $newNotes);
        }
        return $notes;
    }


    public static function getLatestVersion()
    {
        $curVersion = '4.0';
        $updateModel = new \IpUpdate\Library\Model\Migration();
        $scripts = $updateModel->getScriptsFromVersion($curVersion);
        $latestVersion = $curVersion;
        foreach ($scripts as $script) {
            $latestVersion = $script->getDestinationVersion();
        }
        return $latestVersion;
    }

}