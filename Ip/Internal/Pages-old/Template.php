<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Pages;


class Template {


    public static function addLayout ($content) {
        return
'<!DOCTYPE html>
<html>
<head>
    '.ipResponse()->generateHead().'
    <link href="' . ipFileUrl('Ip/Internal/Ip/assets/fonts/font-awesome/font-awesome.css') . '" type="text/css" rel="stylesheet" media="screen" />
    <link href="' . ipFileUrl('Ip/Internal/Ip/assets/bootstrap/bootstrap.css') . '" type="text/css" rel="stylesheet" media="screen" />
    <link href="' . ipFileUrl('Ip/Internal/Pages/assets/pages.css') . '" type="text/css" rel="stylesheet" media="screen" />
    <link href="' . ipFileUrl('Ip/Internal/Pages/jquery-ui/jquery-ui.css') . '" type="text/css" rel="stylesheet" media="screen" />
    '.ipResponse()->generateJavascript().'
    <script type="text/javascript" src="' . ipFileUrl('Ip/Internal/Ip/assets/js/default.js') . '"></script>
    <script type="text/javascript" src="' . ipFileUrl('Ip/Internal/Ip/assets/bootstrap/bootstrap.js') . '"></script>
    <script type="text/javascript" src="' . ipFileUrl('Ip/Internal/Pages/jstree/jquery.cookie.js') . '"></script>
    <script type="text/javascript" src="' . ipFileUrl('Ip/Internal/Pages/jstree/jquery.hotkeys.js') . '"></script>
    <script type="text/javascript" src="' . ipFileUrl('Ip/Internal/Pages/jstree/jquery.jstree.js') . '"></script>
    <script type="text/javascript" src="' . ipFileUrl('Ip/Internal/Pages/assets/pages.js') . '"></script>
    <script type="text/javascript" src="' . ipFileUrl('Ip/Internal/Pages/assets/zoneProperties.js') . '"></script>
    <script type="text/javascript" src="' . ipFileUrl('Ip/Internal/Pages/assets/languageProperties.js') . '"></script>
    <script type="text/javascript" src="' . ipFileUrl('Ip/Internal/Pages/assets/layout.js') . '"></script>
    <script type="text/javascript" src="' . ipFileUrl('Ip/Internal/Pages/jquery-ui/jquery-ui.js') . '"></script>
</head>
<body>
'.$content.'
</body>
</html>
';
    }

    public static function content ($data) {
        $answer = '';

        $answer .=
'
    <script type="text/javascript">
        var imageDir= \''.$data['imageDir'].'\';
        var deleteConfirmText= \''.addslashes(__('Do you really want to delete?', 'ipAdmin')).'\';

        var textSave = \''.addslashes(__('Save', 'ipAdmin')).'\';
        var textCancel = \''.addslashes(__('Cancel', 'ipAdmin')).'\';
        var textDelete = \''.addslashes(__('Delete', 'ipAdmin')).'\';
        var textEdit = \''.addslashes(__('Edit', 'ipAdmin')).'\';
        var textNewPage = \''.addslashes(__('New page', 'ipAdmin')).'\';
        var textCopy = \''.addslashes(__('Copy', 'ipAdmin')).'\';
        var textPaste = \''.addslashes(__('Paste', 'ipAdmin')).'\';
    </script>
    <div class="ip">
        <div id="sideBar" class="ui-widget-content ui-resizable">
            <div id="controlls">
                <ul>
                    <button id="buttonNewPage" class="btn btn-default" disabled="disabled" role="button" aria-disabled="false">
                        <i class="fa fa-file-o"></i>
                        '.__('New page', 'ipAdmin').'
                    </button>
                    <button id="buttonDeletePage" class="btn btn-default" disabled="disabled" role="button" aria-disabled="false">
                        <i class="fa fa-trash-o"></i>
                        '.__('Delete', 'ipAdmin').'
                    </button>
                    <button id="buttonCopyPage" class="btn btn-default" disabled="disabled" role="button" aria-disabled="false">
                        <i class="fa fa-copy"></i>
                        '.__('Copy', 'ipAdmin').'
                    </button>
                    <button id="buttonPastePage" class="btn btn-default" disabled="disabled" role="button" aria-disabled="false">
                        <i class="fa fa-paste"></i>
                        '.__('Paste', 'ipAdmin').'
                    </button>
                </ul>    
            </div>
            <div id="tree"> </div>
            <div class="clear"><!-- --></div>
        </div>
        <div id="pageProperties" class="ui-widget-content"></div>

        <div class="modal fade" id="createPageForm" tabindex="-1" role="dialog" aria-labelledby="createPageFormLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="createPageFormLabel">'.__('New page', 'ipAdmin').'</h4>
                    </div>
                    <div class="modal-body">
                        <form role="form">
                            <div class="form-group">
                                <label for="createPagenavigationTitle">'.__('Button title', 'ipAdmin').'</label>
                                <input type="text" class="form-control" id="createPagenavigationTitle" name="navigationTitle" />
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">'.__('Cancel', 'ipAdmin').'</button>
                        <button type="button" class="btn btn-primary ipsSubmit">'.__('Submit', 'ipAdmin').'</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="treePopup"></div>
';
        return $answer;
    }


    public static function generatePageProperties ($tabs) {
        $answer = '';

        $tabsList = '';
        $contentList = '';

        foreach ($tabs as $tabKey => $tab) {
            $tabsList .=
'
<li>
    <a href="#propertiesTabs-'.($tabKey + 1).'" data-toggle="tab">'.htmlspecialchars($tab['title']).'</a>
</li>
';

            $contentList .=
'
<div id="propertiesTabs-'.($tabKey + 1).'" class="tab-pane">
'.$tab['content'].'
</div>
';
        }

        $answer .=
'
    <ul class="nav nav-tabs" id="propertiesTabs">
        '.$tabsList.'
    </ul>
    <div class="tab-content">
        '.$contentList.'
    </div>
    <script>
        $(function () {
            $(\'#propertiesTabs a:first\').tab(\'show\')
        })
    </script>
';

        return $answer;
    }


    public static function generateTabGeneral () {
        $answer = '';
        $element = new \Ip\Page('null', 'left');
        $answer .=
'
<form id="formGeneral" role="form">
    <div class="form-group">
        <label for="generalnavigationTitle">'.__('Button title', 'ipAdmin').'</label>
        <input id="generalnavigationTitle" name="navigationTitle" value="'.htmlspecialchars($element->getNavigationTitle()).'" type="text" class="form-control" />
    </div>
    <div class="form-group">
        <label for="generalVisible">'.__('Visible', 'ipAdmin').'</label>
        <div class="checkbox">
            <label>
                <input id="generalVisible" class="stdModBox" type="checkbox" name="visible" '.($element->isVisible() ? 'checked="checked"' : '' ).' />
                '.__('Visible', 'ipAdmin').'
            </label>
        </div>
    </div>
    <div class="form-group">
        <label for="generalCreatedOn">'.__('Created on', 'ipAdmin').'</label>
        <span class="error" id="createdOnError"></span>
        <input id="generalCreatedOn" name="createdOn" value="'.htmlspecialchars(substr($element->getCreatedOn(), 0, 10)).'" type="text" class="form-control" />
    </div>
    <div class="form-group">
        <label for="lastModifiedError">'.__('Last modified', 'ipAdmin').'</label>
        <span class="error" id="lastModifiedError"></span>
        <input id="generalLastModified" name="lastModified" value="'.htmlspecialchars(substr($element->getLastModified(), 0, 10)).'" type="text" class="form-control" />
    </div>
    <button class="btn btn-primary" type="submit">'.__('Save', 'ipAdmin').'</button>
</form>
';

        return $answer;
    }

    public static function generateTabSEO () {

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
            <img class="linkList" id="internalLinkingIcon" src="' . ipFileUrl('Ip/Internal/Pages/img/list.gif') . '" /><br />
        </p>

        <input class="submit" type="submit" value="'.__('Save', 'ipAdmin').'" />

</form>
';

        return $answer;
    }


}


