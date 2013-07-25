<?php
/**
 * @package ImpressPages

 *
 */

namespace IpUpdate\Gui;



define ('IP_ERROR_404', '<p>Requested page not found.</p>');
define ('IP_ERROR_NO_INFORMATION', '<p>This script has no information about your system.</p>');





class Translation
{
    private static $translations;
    private static $instance;
    
    public function __construct()
    {
        $translations = array ();
        
        $translations['global_title'] = 'ImpressPages CMS update wizard';
        $translations['step_completed_title'] = 'Success';
        $translations['step_completed_text'] = '
<p>Your system has been successfully updated.</p>

<p>
<a href="../">Front page</a>
</p><p>
<a href="../admin.php">Administration page</a>
</p>
';
        $translations['button_proceed'] = 'Proceed';
        $translations['error_write_permission_title'] = 'File write failed';
        $translations['error_write_permission_text'] = '
<p>
To proceed please make following directory or file writable <b>[[file]]</b>
</p>
';
        
        $translations['error_curl_required_title'] = 'CURL required';
        $translations['error_curl_required_text'] = '
<p>
Update process requires CURL module.
</p>
';
        
        $translations['error_in_progress_title'] = 'Another update process in progress';
        $translations['error_in_progress_text'] = '
<p>Last update process failed or another update process is in progress.</p>
<p>If you are sure that there is no another update instanace running, you can reset the lock and start over again.</p>
';
        $translations['button_reset_lock'] = 'Reset the lock';
        
        
            self::$translations = $translations;

        
    }
    
    
    public static function getInstance()
    {
        if (!static::$instance)
        {
            static::$instance = new Translation() ;
        }
        return static::$instance;
    }
    

    /**
     * 
     * @param string $key
     * @return string
     */
    public function translate($key)
    {
        return self::$translations[$key];
    }
    
    
    
    
    
    
    
}