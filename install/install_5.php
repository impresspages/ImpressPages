<?php
/**
 * @package ImpressPages
 *
 *
 */

if (!defined('INSTALL')) exit;

output('<h1>'.IP_STEP_COMPLETED_LONG.'</h1>'.IP_FINISH_MESSAGE.'<img width="1px" height="1px" style="border: none;" src="../ip_cron.php" alt=""/>');
/**
 * autocron is required in case first visit is to administration panel.
 * In this case cron tries to delete old revisions in parallel to administration panel load.
 * Some issues are avoided while executing cron before going to administration panel.
 */

unset($_SESSION['step']);



?>