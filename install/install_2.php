<?php 
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2009 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */

if (INSTALL!="true") exit;
 
 
$licenseFile = "../ip_license.html";
$fh = fopen($licenseFile, 'r');
$license = fread($fh, filesize($licenseFile));
fclose($fh);
 
	
output('
<h1>'.IP_STEP_LICENSE_LONG.'</h1>	
<p>
ImpressPages - Content Management System<br/>
Copyright 2009 by <a href="http://www.aproweb.eu">JSC Apro Media</a>
</p>
<p>
This program is free software: you can redistribute it and/or modify it under the terms of the <a href="../ip_license.html">GNU General Public License</a> as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
</p><p>
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
</p><p>
You are required to keep these "Appropriate Legal Notices" intact as specified in GPL3 section 5(d) and 7(b).
</p><p>


<a class="button_act" href="?step=3">'.IP_ACCEPT.'</a>
<a class="button" href="?step=1">'.IP_BACK.'</a>

');


complete_step(2);
?>