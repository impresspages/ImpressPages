<?php
/**
 * @package ImpressPages

 *
 */

namespace IpUpdate\Library\Migration;


abstract class General
{

    private $versionInfo;
    /**
     * @param array $cf parsed installation configuration
     * @return \Library\Migration\Result
     */
    public abstract function process($cf);

    /**
     * @return string
     */
    public abstract function getSourceVersion();

    /**
     * @return string
     */
    public abstract function getDestinationVersion();
    
    public function getNotes($cf)
    {
        return array();
    }


    public function getDownloadUrl()
    {
        return $this->getInfoValue('downloadUrl');
    }
    
    public function getMd5()
    {
        return $this->getInfoValue('md5');
    }
    
    
    private function getVersionInfo()
    {
        if (!function_exists('curl_init')) {
            throw new \IpUpdate\Library\UpdateException("Can't get download URL", \IpUpdate\Library\UpdateException::CURL_REQUIRED);
        }

        $ch = curl_init();
        
        

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 1800, // set this to 30 min so we dont timeout
            CURLOPT_URL => \IpUpdate\Library\Model\Environment::instance()->getImpressPagesAPIUrl(),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => 'module_group=service&module_name=communication&action=getVersionInfo&version='.$this->getDestinationVersion()
        );

        curl_setopt_array($ch, $options);
        
        $jsonAnswer = curl_exec($ch);
        
        $answer = json_decode($jsonAnswer);
        
        if ($answer === null) {
            throw new \IpUpdate\Library\Exception("Can't get version info about version ".$this->getDestinationVersion().". Server answer: ".$jsonAnswer." ", \IpUpdate\Library\UpdateException::UNKNOWN);
        }
        
        $this->versionInfo = $answer;
    }
    
    private function getInfoValue($key)
    {
        if (!$this->versionInfo) {
            $this->getVersionInfo();
        }
        return $this->versionInfo->$key;
    }
}