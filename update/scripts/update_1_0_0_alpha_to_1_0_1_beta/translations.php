<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license		GNU/GPL, see ip_license.html
 */

namespace update_1_0_0_alpha_to_1_0_1_beta; 

if(!defined('THEME_DIR'))
  define('THEME_DIR', '');
if(!defined('AUDIO_DIR'))
  define('AUDIO_DIR', '');
//1 step
const MOVE_AND_MAKE_CONFIG_WRITEABLE = '
<p>1. Copy "config.php" file from "includes" to root directory</p>
<p>2. Rename "config.php" file to "ip_config.php"</p>
<p>3. Make "ip_config.php" file writeable</p>
';
const CANCEL_CONFIG_WRITEABLE = '<p>Cancel write permissions for file "ip_config.php".</p>';


const REMOVE_DIRECTORIES = '<p><b>Please remove following directories:</b></p>';
const REMOVE_FILES = '<p><b>Please remove following files:</b></p>';


const UPLOAD_DIRECTORIES = '<p><b>Please upload following folders from downloaded archive:</b></p>';
const UPLOAD_FILES = '<p><b>Please upload following files from downloaded archive:</b></p>';


define ('MAKE_TEMPLATE_WRITEABLE', '<p>Make folder '.THEME_DIR.' writeable including subfolders and files. Theme files will be automatically updated to make them compatible with new core version.</p>');
define ('CANCEL_TEMPLATE_WRITEABLE', '<p>Cancel write permissions for folder '.THEME_DIR.'.</p>');


define ('MAKE_AUDIO_WRITEABLE', '<p>Make folder '.AUDIO_DIR.' writeable including subfolders and files.</p>');
