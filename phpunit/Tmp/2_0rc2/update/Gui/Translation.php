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
        self::$translations = array (
            'global_title' => 'ImpressPages CMS update wizard',
            'step_completed_title' => 'Success',
            'step_completed_text' => '
<p>Your system has been successfully updated.</p>

<p>
<a href="../">Front page</a>
</p><p>
<a href="../admin.php">Administration page</a>
</p>
            ',
            'button_proceed' => 'Proceed',
            'error_write_permission_title' => 'File write failed',
            'error_write_permission_text' => '
<p>
To proceed please make following directory or file writable <b>[[file]]</b>
</p>
            '
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