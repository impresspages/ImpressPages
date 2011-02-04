<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */

namespace update_1_0_2_beta_to_1_0_3_beta;

if (!defined('CMS')) exit;

define ('REMOVE_DIRECTORIES','<p><b>Please remove following directories:</b></p>');
define ('REMOVE_FILES','<p><b>Please remove following files:</b></p>');
define ('UPLOAD_DIRECTORIES', '<p><b>Please upload following folders from downloaded archive:</b></p>');
define ('UPLOAD_FILES', '<p><b>Please upload following files from downloaded archive:</b></p>');
define ('MAKE_TEMPLATE_WRITEABLE', '<p>Make folder '.THEME_DIR.' writeable including subfolders and files. Theme files will be automatically updated to make them compatible with new core version.</p>');
define ('CANCEL_TEMPLATE_WRITEABLE', '<p>Cancel write permissions for folder '.THEME_DIR.'.</p>');
define ('LATER', 'I will do that later.');
