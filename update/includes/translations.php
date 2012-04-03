<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2012 ImpressPages LTD.
 * @license see ip_license.html
 */


if (!defined('CMS')) exit;

define('IP_OK', 'Yes');
define('IP_ERROR', 'No');
define('IP_NEXT', 'Next');
define('IP_CHECK', 'Check again');

define ('IP_INSTALLATION', 'ImpressPages CMS update wizard');


define ('IP_STEP_BACKUP', 'Backup');
define ('IP_STEP_BACKUP_INTRODUCTION', '
<p>Backup all your files and database before starting the update process.</p>
<br/>
<p>Your current ImpressPages version is <b>[[current_version]]</b></p>
<br/>
<p>Update to <b>[[new_version]]</b></p>
');
define ('IP_OLD_VERSION_WARNING', '
<hr/>
<P><span style="color: red; font-weight: bold">ATTENTION</span></P>
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
You need manually replace your current ip_content.css file
 (ip_themes/lt_pagan/ip_content.css) to one from  downloaded archive.
 If you have made some changes to original files, please replicate them to the new one.
</p>
<p>If you are using other theme, you need manually tweek you CSS
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
define ('IP_ERROR_UNINSTALL_COPY_CONTENT', '
<p>This ImpressPages CMS version duplicate all "Copy content" plugin functionallity. Please uninstall this plugin and try again.</p>
<p>
<b>Copy content plugin removal instructions:</b><br />
<ul>
	<li>Login to administration area</li>
	<li>Open "Developer -> modules" tab</li>
	<li>Press yellow icon on line "Standard"</li>
	<li>Press trash icon on line "Copy content"</li>
</ul>
</p>
<p>
After you will delete "Copy content" plugin, come back here and proceed update process.
</p>
');

