<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */


if (!defined('CMS')) exit;

define('IP_OK', 'Yes');
define('IP_ERROR', 'No');
define('IP_NEXT', 'Next');
define('IP_CHECK', 'Check again');

define ('IP_INSTALLATION', 'ImpressPages CMS update wizard');


define ('IP_STEP_BACKUP', 'Backup');
define ('IP_STEP_BACKUP_INTRODUCTION', '
<p>Backup all your files and database before starting the update process. The script will automatically modify CSS files of your current theme. So, check the look of your website after update.</p>
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
<p>This update requires to clear the cache. To do so, login to administration area, go to "Administrator => System" tab and press "Clear cache"</p>
<p>
<a href="../">Front page</a>
</p>
<p>
<a href="../admin.php">Administration page</a>
</p>
');
define ('IP_ERROR_404', '<p>Requested page not found.</p>');
define ('IP_ERROR_NO_INFORMATION', '<p>This script have no information about your system.</p>');
