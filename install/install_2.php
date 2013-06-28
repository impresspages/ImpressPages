<?php
/**
 * @package ImpressPages
 *
 *
 */

if (!defined('INSTALL')) exit;


$licenseFile = "../ip_license.html";
$fh = fopen($licenseFile, 'r');
$license = fread($fh, filesize($licenseFile));
fclose($fh);


output('
<h1>'.IP_STEP_LICENSE_LONG.'</h1>	
<p>
ImpressPages - Content Management System<br/>
Copyright '.date("Y").' by <a href="http://www.impresspages.org">ImpressPages LTD</a>
</p>
<p>This program is free software: you can redistribute it and/or modify it under the terms of the <a href="../ip_license.html">GNU General Public License or MIT License</a>.</p>
<p>This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.</p>
<p>You are required to keep these "Appropriate Legal Notices" intact as specified in GPL3 section 5(d) and 7(b) and MIT.</p>
<p>&nbsp;</p>
<p><strong>Automatic updates disclaimer</strong></p>
<p>ImpressPages is configured to check for the updates automatically. This requires for the technical data of the website to be transfered to ImpressPages service servers. This process does not transfer any personal data or any part of the website\'s content.</p>
<p>
<a class="button_act" href="?step=3">'.IP_ACCEPT.'</a>
<a class="button" href="?step=1">'.IP_BACK.'</a>
</p>
');


complete_step(2);
?>