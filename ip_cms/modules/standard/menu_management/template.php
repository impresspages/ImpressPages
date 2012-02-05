<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license	GNU/GPL, see ip_license.html
 */
namespace Modules\standard\menu_management;
if (!defined('BACKEND')) exit;


class Template {


    public static function addLayout ($content) {
        return
'<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ImpressPages</title>
    <link href="'.BASE_URL.MODULE_DIR.'standard/menu_management/menu_management.css" type="text/css" rel="stylesheet" media="screen" />
    <link href="'.BASE_URL.MODULE_DIR.'standard/menu_management/jquery-ui/jquery-ui.css" type="text/css" rel="stylesheet" media="screen" />
    <script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/default.js"></script>
    <script type="text/javascript" src="'.BASE_URL.LIBRARY_DIR.'js/jquery/jquery.js"></script>
    <script type="text/javascript" src="'.BASE_URL.MODULE_DIR.'standard/menu_management/jstree/jquery.cookie.js"></script>
    <script type="text/javascript" src="'.BASE_URL.MODULE_DIR.'standard/menu_management/jstree/jquery.hotkeys.js"></script>
    <script type="text/javascript" src="'.BASE_URL.MODULE_DIR.'standard/menu_management/jstree/jquery.jstree.js"></script>
    <script type="text/javascript" src="'.BASE_URL.MODULE_DIR.'standard/menu_management/menu_management.js"></script>
    <script type="text/javascript" src="'.BASE_URL.MODULE_DIR.'standard/menu_management/jquery-ui/jquery-ui.js"></script>
</head>
<body>
'.$content.'
</body>
</html>
';
    }

    public static function content ($data) {
        global $parametersMod;
        $answer = '';

        $answer .=
'
    <script type="text/javascript">
        var postURL = \''.$data['postURL'].'\';
        var imageDir= \''.$data['imageDir'].'\'; 
        var deleteConfirmText= \''.addslashes($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'question_delete')).'\';

        var textSave = \''.addslashes($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'save')).'\';
        var textCancel = \''.addslashes($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'cancel')).'\';
        var textDelete = \''.addslashes($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'delete')).'\';
        var textEdit = \''.addslashes($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'edit')).'\';
        var textNewPage = \''.addslashes($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'new_page')).'\';
        var textCopy = \''.addslashes($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'copy')).'\';
        var textPaste = \''.addslashes($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'paste')).'\';
    </script>
    <div>
    	<div id="sideBar" class="ui-widget-content ui-resizable">
    		<div id="controlls">
                <ul>
                    <button id="buttonNewPage" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button" aria-disabled="false">
                    	<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
                    	<span class="ui-button-text">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'new_page')).'</span>
                    </button>
                    <button id="buttonDeletePage" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button" aria-disabled="false">
                    	<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
                    	<span class="ui-button-text">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'delete')).'</span>
                    </button>
                    <button id="buttonCopyPage" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button" aria-disabled="false">
                    	<span class="ui-button-icon-primary ui-icon ui-icon-copy"></span>
                    	<span class="ui-button-text">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'copy')).'</span>
                    </button>
                    <button id="buttonPastePage" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary ui-state-disabled" role="button" aria-disabled="false">
                    	<span class="ui-button-icon-primary ui-icon ui-icon-copy"></span>
                    	<span class="ui-button-text">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'paste')).'</span>
                    </button>
                </ul>    
    		</div>
    		<div id="tree"> </div>
    		<div class="clear"><!-- --></div>
    	</div>
    	<div id="pageProperties" class="ui-widget-content"></div>
    </div>	
	<div id="createPageForm" title="'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'new_page')).'">
		<form id="formCreatePage">
            <label for="createPageButtonTitle">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'button_title')).'</label>
            <input id="createPageButtonTitle" name="buttonTitle" value="" />
		</form>
	</div>    	
    <div id="treePopup"></div>
';
        return $answer;
    }


    public static function generatePageProperties ($tabs) {
        global $parametersMod;
        $answer = '';

        $tabsList = '';
        $contentList = '';


        foreach ($tabs as $tabKey => $tab) {
            $tabsList .=
'
<li>
	<a href="#propertiesTabs-'.($tabKey + 1).'">'.htmlspecialchars($tab['title']).'</a>
</li>
';

            $contentList .=
'
<div id="propertiesTabs-'.($tabKey + 1).'">
'.$tab['content'].'
</div>
';
        }

        $answer .=
'
    <ul class="tabs">
    '.$tabsList.'
    </ul>    
    '.$contentList.'    
';

        return $answer;
    }


    public static function generateTabGeneral () {
        global $parametersMod;
        $answer = '';
        $element = new \Frontend\Element('null', 'left');
        $answer .=
'
<form id="formGeneral">
	<p class="field">
        <label for="generalButtonTitle">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'button_title')).'</label>
        <input id="generalButtonTitle" name="buttonTitle" value="'.htmlspecialchars($element->getButtonTitle()).'" /><br />
    </p>
	<p class="field">
        <label for="generalVisible">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'visible')).'</label>
    	<input id="generalVisible" class="stdModBox" type="checkbox" name="visible" '.($element->getVisible() ? 'checked="yes"' : '' ).' /><br />
    </p>
	<p class="field">
    	<label for="generalCreatedOn">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'created_on')).'</label>
    	<span class="error" id="createdOnError"></span>
    	<input id="generalCreatedOn" name="createdOn" value="'.htmlspecialchars(substr($element->getCreatedOn(), 0, 10)).'" /><br />
    </p>
	<p class="field">
    	<label for="lastModifiedError">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'last_modified')).'</label>
    	<span class="error" id="lastModifiedError"></span>
    	<input id="generalLastModified" name="lastModified" value="'.htmlspecialchars(substr($element->getLastModified(), 0, 10)).'" /><br />
	</p>    
    <input class="submit" type="submit" value="'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'save')).'" />
</form>
';

        return $answer;
    }

    public static function generateTabSEO () {
        global $parametersMod;

        $answer = '';
        $element = new \Frontend\Element('null', 'left');

        $answer .=
'
<form id="formSEO">

	<p class="field">
        <label for="seoPageTitle">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'page_title')).'</label>
        <input id="seoPageTitle" name="pageTitle" value="'.htmlspecialchars($element->getPageTitle()).'" /><br />
    </p>
	<p class="field">
        <label for="seoKeywords">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'keywords')).'</label>
        <textarea id="seoKeywords" name="keywords">'.htmlspecialchars($element->getKeywords()).'</textarea><br />
    </p>
	<p class="field">
        <label for="seoDescription">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'description')).'</label>
        <textarea id="seoDescription" name="description">'.htmlspecialchars($element->getDescription()).'</textarea><br />
    </p>
	<p class="field">
        <label for="seoUrl">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'url')).'</label>
        <input id="seoUrl" name="url" value="'.htmlspecialchars($element->getURL()).'" /><br />
    </p>

    <input class="submit" type="submit" value="'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'save')).'" />

</form>  
';

        return $answer;
    }

    public static function generateTabAdvanced () {
        global $parametersMod;
        $element = new \Frontend\Element('null', 'left');

        $answer = '';

        $answer .=
'
<form id="formAdvanced">
        <label>'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'type')).'</label>        
        <p class="field">
            <input id="typeDefault" class="stdModBox" name="type" value="default" '.($element->getType() == 'default' ? 'checked="checkded"' : '' ).' type="radio" />
            <label for="typeDefault" class="small">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'no_redirect')).'</label><br />
        </p>
        <p class="field">
            <input id="typeInactive" class="stdModBox" name="type" value="inactive" '.($element->getType() == 'inactive' ? 'checked="checkded"' : '' ).'type="radio" />
            <label for="typeInactive" class="small">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'inactive')).'</label><br />
        </p>
        <p class="field">
            <input id="typeSubpage" class="stdModBox" name="type" value="subpage" '.($element->getType() == 'subpage' ? 'checked="checkded"' : '' ).'type="radio" />
            <label for="typeSubpage" class="small">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'redirect_to_subpage')).'</label><br />
            
        </p>
        
        <span class="error" id="redirectURLError"></span>
        <p class="field">
            <input id="typeRedirect" class="stdModBox" name="type" value="redirect" '.($element->getType() == 'redirect' ? 'checked="checkded"' : '' ).'type="radio" />
            <label for="typeRedirect" class="small">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'redirect_to_external_page')).'</label><br/>       
            <input autocomplete="off" name="redirectURL" value="'.$element->getRedirectUrl().'">
            <img class="linkList" id="internalLinkingIcon" src="'.BASE_URL.MODULE_DIR.'standard/menu_management/img/list.gif" /><br />
        </p>
        <p class="field">
            <label for="generalVisible">'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'rss')).'</label>
            <input id="generalVisible" class="stdModBox" type="checkbox" name="rss" '.($element->getRSS() ? 'checked="yes"' : '' ).' /><br />
        </p>
        
        <input class="submit" type="submit" value="'.htmlspecialchars($parametersMod->getValue('standard', 'menu_management', 'admin_translations', 'save')).'" />

</form>
';

        return $answer;
    }


}


