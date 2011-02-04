<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

if (!defined('INSTALL')) exit;

output('<h1>'.IP_STEP_COMPLETED_LONG.'</h1>'.IP_FINISH_MESSAGE);
unset($_SESSION['step']);



?>