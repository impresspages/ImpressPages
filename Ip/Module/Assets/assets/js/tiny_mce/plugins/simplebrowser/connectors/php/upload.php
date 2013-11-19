<?php

/*
 * FCKeditor - The text editor for Internet - http://www.fckeditor.net
 * Copyright (C) 2003-2007 Frederico Caldeira Knabben
 *
 * == BEGIN LICENSE ==
 *
 * Licensed under the terms of any of the following licenses at your
 * choice:
 *
 *  - GNU General Public License Version 2 or later (the "GPL")
 *    http://www.gnu.org/licenses/gpl.html
 *
 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *
 *  - Mozilla Public License Version 1.1 or later (the "MPL")
 *    http://www.mozilla.org/MPL/MPL-1.1.html
 *
 * == END LICENSE ==
 *
 * This is the "File Uploader" for PHP.
 */

 
 /*ImpressPages security*/
	
	error_reporting(E_ALL|E_STRICT);
	ini_set('display_errors', '1');
	

	define("BACKEND", "true");  // make sure files are accessed through admin.

    if(file_exists('../../../../../../../../../ms_config.php')) {
        require_once ('../../../../../../../../../ms_config.php');
    } elseif (is_file('../../../../../../../ip_config.php')) {
        require_once ('../../../../../../../ip_config.php');
    } else {
        require_once ('../../../../../../../../ip_config.php');
    }

  session_name(ipGetConfig()->getRaw('SESSION_NAME'));
  session_start();
	$admin = false;
  if(isset($_SESSION['backend_session']) && isset($_SESSION['backend_session']['userId']) && isset($_SESSION['backend_session']['userId']) != null){
    $admin = true;
  }

	if(!$admin)
		exit;

 /*eof ImpressPages security*/ 
 
require('./config.php');
require('./util.php');
require('./io.php');
require('./commands.php');
require('./phpcompat.php');

function SendError( $number, $text )
{
	SendUploadResults( $number, '', '', $text ) ;
}


// Check if this uploader has been enabled.
if ( !$Config['Enabled'] )
	SendUploadResults( '1', '', '', 'This file uploader is disabled. Please check the "editor/filemanager/connectors/php/config.php" file' ) ;

$sCommand = 'QuickUpload' ;

// The file type (from the QueryString, by default 'File').
$sType = isset( $_GET['Type'] ) ? $_GET['Type'] : 'Image' ;

$sCurrentFolder	= GetCurrentFolder() ; 

// Is enabled the upload?
if ( ! IsAllowedCommand( $sCommand ) ) 
	SendUploadResults( '1', '', '', 'The ""' . $sCommand . '"" command isn\'t allowed' ) ;

// Check if it is an allowed type.
if ( !IsAllowedType( $sType ) )
    SendUploadResults( 1, '', '', 'Invalid type specified' ) ;


FileUpload( $sType, $sCurrentFolder, $sCommand )

?>