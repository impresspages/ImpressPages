<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Module\Pages;


class Template {


    public static function addLayout ($content) {
        return
'<!DOCTYPE html>
<html>
<head>
    '.ipPrintHead(false).'
    <link href="' . ipConfig()->coreModuleUrl('Assets/assets/fonts/font-awesome/font-awesome.css') . '" type="text/css" rel="stylesheet" media="screen" />
    <link href="' . ipConfig()->coreModuleUrl('Assets/assets/css/bootstrap/bootstrap.css') . '" type="text/css" rel="stylesheet" media="screen" />
    <link href="' . ipConfig()->coreUrl('Ip/Module/Pages/assets/pages.css') . '" type="text/css" rel="stylesheet" media="screen" />
    <link href="' . ipConfig()->coreUrl('Ip/Module/Pages/jquery-ui/jquery-ui.css') . '" type="text/css" rel="stylesheet" media="screen" />
    '.ipPrintJavascript(false).'
    <script type="text/javascript" src="' . ipConfig()->coreModuleUrl('Assets/assets/js/default.js') . '"></script>
    <script type="text/javascript" src="' . ipConfig()->coreModuleUrl('Assets/assets/css/bootstrap/bootstrap.js') . '"></script>
    <script type="text/javascript" src="' . ipConfig()->coreUrl('Ip/Module/Pages/jstree/jquery.cookie.js') . '"></script>
    <script type="text/javascript" src="' . ipConfig()->coreUrl('Ip/Module/Pages/jstree/jquery.hotkeys.js') . '"></script>
    <script type="text/javascript" src="' . ipConfig()->coreUrl('Ip/Module/Pages/jstree/jquery.jstree.js') . '"></script>
    <script type="text/javascript" src="' . ipConfig()->coreUrl('Ip/Module/Pages/assets/pages.js') . '"></script>
    <script type="text/javascript" src="' . ipConfig()->coreUrl('Ip/Module/Pages/assets/zoneProperties.js') . '"></script>
    <script type="text/javascript" src="' . ipConfig()->coreUrl('Ip/Module/Pages/assets/languageProperties.js') . '"></script>
    <script type="text/javascript" src="' . ipConfig()->coreUrl('Ip/Module/Pages/assets/layout.js') . '"></script>
    <script type="text/javascript" src="' . ipConfig()->coreUrl('Ip/Module/Pages/jquery-ui/jquery-ui.js') . '"></script>
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
        var imageDir= \''.$data['imageDir'].'\';
        var deleteConfirmText= \''.addslashes(__('Do you really want to delete?', 'ipAdmin')).'\';

        var textSave = \''.addslashes(__('Save', 'ipAdmin')).'\';
        var textCancel = \''.addslashes($parametersMod->getValue('Pages.cancel')).'\';
        var textDelete = \''.addslashes(__('Delete', 'ipAdmin')).'\';
        var textEdit = \''.addslashes(__('Edit', 'ipAdmin')).'\';
        var textNewPage = \''.addslashes(__('New page', 'ipAdmin')).'\';
        var textCopy = \''.addslashes(__('Copy', 'ipAdmin')).'\';
        var textPaste = \''.addslashes($parametersMod->getValue('Pages.paste')).'\';
    </script>
    <div>
    	<div id="sideBar" class="ui-widget-content ui-resizable">
    		<div id="controlls">
                <ul>
                    <button id="buttonNewPage" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary ui-state-disabled" role="button" aria-disabled="false">
                    	<span class="ui-button-icon-primary ui-icon ui-icon-document"></span>
                    	<span class="ui-button-text">'.__('New page', 'ipAdmin').'</span>
                    </button>
                    <button id="buttonDeletePage" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary ui-state-disabled" role="button" aria-disabled="false">
                    	<span class="ui-button-icon-primary ui-icon ui-icon-trash"></span>
                    	<span class="ui-button-text">'.__('Delete', 'ipAdmin').'</span>
                    </button>
                    <button id="buttonCopyPage" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary ui-state-disabled" role="button" aria-disabled="false">
                    	<span class="ui-button-icon-primary ui-icon ui-icon-copy"></span>
                    	<span class="ui-button-text">'.__('Copy', 'ipAdmin').'</span>
                    </button>
                    <button id="buttonPastePage" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary ui-state-disabled" role="button" aria-disabled="false">
                    	<span class="ui-button-icon-primary ui-icon ui-icon-copy"></span>
                    	<span class="ui-button-text">'.htmlspecialchars($parametersMod->getValue('Pages.paste')).'</span>
                    </button>
                </ul>    
    		</div>
    		<div id="tree"> </div>
    		<div class="clear"><!-- --></div>
    	</div>
    	<div id="pageProperties" class="ui-widget-content"></div>
    </div>	
	<div id="createPageForm" title="'.__('New page', 'ipAdmin').'">
		<form id="formCreatePage">
            <label for="createPageButtonTitle">'.__('Button title', 'ipAdmin').'</label>
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
        $element = new \Ip\Page('null', 'left');
        $answer .=
'
<form id="formGeneral">
	<p class="field">
        <label for="generalButtonTitle">'.__('Button title', 'ipAdmin').'</label>
        <input id="generalButtonTitle" name="buttonTitle" value="'.htmlspecialchars($element->getButtonTitle()).'" /><br />
    </p>
	<p class="field">
        <label for="generalVisible">'.__('Visible', 'ipAdmin').'</label>
    	<input id="generalVisible" class="stdModBox" type="checkbox" name="visible" '.($element->getVisible() ? 'checked="yes"' : '' ).' /><br />
    </p>
	<p class="field">
    	<label for="generalCreatedOn">'.__('Created on', 'ipAdmin').'</label>
    	<span class="error" id="createdOnError"></span>
    	<input id="generalCreatedOn" name="createdOn" value="'.htmlspecialchars(substr($element->getCreatedOn(), 0, 10)).'" /><br />
    </p>
	<p class="field">
    	<label for="lastModifiedError">'.__('Last modified', 'ipAdmin').'</label>
    	<span class="error" id="lastModifiedError"></span>
    	<input id="generalLastModified" name="lastModified" value="'.htmlspecialchars(substr($element->getLastModified(), 0, 10)).'" /><br />
	</p>    
    <input class="submit" type="submit" value="'.__('Save', 'ipAdmin').'" />
</form>
';

        return $answer;
    }

    public static function generateTabSEO () {
        global $parametersMod;

        $answer = '';
        $element = new \Ip\Page('null', 'left');

        $answer .=
'
<form id="formSEO">

	<p class="field">
        <label for="seoPageTitle">'.__('Meta title', 'ipAdmin').'</label>
        <input id="seoPageTitle" name="pageTitle" value="'.htmlspecialchars($element->getPageTitle()).'" /><br />
    </p>
	<p class="field">
        <label for="seoKeywords">'.__('Meta keywords', 'ipAdmin').'</label>
        <textarea id="seoKeywords" name="keywords">'.htmlspecialchars($element->getKeywords()).'</textarea><br />
    </p>
	<p class="field">
        <label for="seoDescription">'.__('Meta description', 'ipAdmin').'</label>
        <textarea id="seoDescription" name="description">'.htmlspecialchars($element->getDescription()).'</textarea><br />
    </p>
	<p class="field">
        <label for="seoUrl">'.__('URL', 'ipAdmin').'</label>
        <input id="seoUrl" name="url" value="'.htmlspecialchars($element->getURL()).'" /><br />
    </p>

    <input class="submit" type="submit" value="'.__('Save', 'ipAdmin').'" />

</form>  
';

        return $answer;
    }

    public static function generateTabAdvanced () {
        global $parametersMod;
        $element = new \Ip\Page('null', 'left');

        $answer = '';

        $answer .=
'
<form id="formAdvanced">
        <label>'.__('Type', 'ipAdmin').'</label>
        <p class="field">
            <input id="typeDefault" class="stdModBox" name="type" value="default" '.($element->getType() == 'default' ? 'checked="checkded"' : '' ).' type="radio" />
            <label for="typeDefault" class="small">'.__('Display page content', 'ipAdmin').'</label><br />
        </p>
        <p class="field">
            <input id="typeInactive" class="stdModBox" name="type" value="inactive" '.($element->getType() == 'inactive' ? 'checked="checkded"' : '' ).'type="radio" />
            <label for="typeInactive" class="small">'.__('Inactive (without link on it)', 'ipAdmin').'</label><br />
        </p>
        <p class="field">
            <input id="typeSubpage" class="stdModBox" name="type" value="subpage" '.($element->getType() == 'subpage' ? 'checked="checkded"' : '' ).'type="radio" />
            <label for="typeSubpage" class="small">'.__('Redirect to first sub-page', 'ipAdmin').'</label><br />
            
        </p>
        
        <span class="error" id="redirectURLError"></span>
        <p class="field">
            <input id="typeRedirect" class="stdModBox" name="type" value="redirect" '.($element->getType() == 'redirect' ? 'checked="checkded"' : '' ).'type="radio" />
            <label for="typeRedirect" class="small">'.__('Redirect to external page', 'ipAdmin').'</label><br/>
            <input autocomplete="off" name="redirectURL" value="'.$element->getRedirectUrl().'">
            <img class="linkList" id="internalLinkingIcon" src="' . ipConfig()->coreUrl('Ip/Module/Pages/img/list.gif') . '" /><br />
        </p>
        <p class="field">
            <label for="generalVisible">'.__('RSS', 'ipAdmin').'</label>
            <input id="generalVisible" class="stdModBox" type="checkbox" name="rss" '.($element->getRSS() ? 'checked="yes"' : '' ).' /><br />
        </p>
        
        <input class="submit" type="submit" value="'.__('Save', 'ipAdmin').'" />

</form>
';

        return $answer;
    }


}


