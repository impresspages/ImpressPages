<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
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
        
            self::$translations = $translations;

        );
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