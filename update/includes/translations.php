<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
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

