<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

namespace IpUpdate\Gui;


define('IP_OK', 'Yes');
define('IP_ERROR', 'No');
define('IP_NEXT', 'Next');
define('IP_CHECK', 'Check again');




define ('IP_STEP_BACKUP', 'Backup');

define ('IP_OLD_VERSION_WARNING', '
<hr/>
<P><span style="color: red;manual font-weight: bold">ATTENTION</span></P>
<p>You are updating from [[current_version]].
You need manually add these lines to your theme
layout file (ip_themes/lt_pagan/main.php) before <b>generateJavascript()</b> line:
</p>
<pre>
&lt;?php
    $site->addJavascript(BASE_URL.LIBRARY_DIR.\'js/jquery/jquery.js\');
    $site->addJavascript(BASE_URL.LIBRARY_DIR.\'js/colorbox/jquery.colorbox.js\');
?&gt;
</pre>
<p>
This is done to gain more control over the website for theme designer.
Now ImpressPages core does not include any JavaScript by default. If theme
needs some Javascript, it includes it.

</p>
');
define ('IP_OLD_VERSION_WARNING2', '
<hr/>
<P><span style="color: red; font-weight: bold">ATTENTION</span></P>
<p>You are updating from [[current_version]].
IpForm widget has been introduced since then.
You need manually replace your current ip_content.css and 960.css files
 (ip_themes/lt_pagan/) to ones from downloaded archive.
 If you have made some changes to original files, please replicate those changes on new files.
</p>
<p>If you are using other theme, you need manually tweek your CSS
to style forms.</p>
');
define ('IP_STEP_BACKUP_UPDATE', 'Start Update');

define ('IP_STEP_PROCESS', 'Update Process');
define ('IP_STEP_FINISH', 'Finish');



define ('IP_ERROR_COMPLETED', '
<p>Your system is successfully updated. Please delete "update" folder</p>
<p>
<a href="../">Front page</a>
</p>
<p>
<a href="../admin.php">Administration page</a>
</p>
');
define ('IP_ERROR_404', '<p>Requested page not found.</p>');
define ('IP_ERROR_NO_INFORMATION', '<p>This script has no information about your system.</p>');





class Translation
{
    private static $translations;
    private static $instance;
    
    public function __construct()
    {
        self::$translations = array (
            'global_title' => 'ImpressPages CMS update wizard'
            
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