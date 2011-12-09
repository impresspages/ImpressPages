<div class="ipAdminPanel">
    <div class="ipAdminWidgets">
<?php if(!$manageableRevision){ ?>
        <div class="ipAdminWidgetsDisable">
            <p>
                This is a preview of older revision, created at (
                    <?php echo date("Y-m-d H:i", $currentRevision['created']) ?>
                ).
                <a href="#">Publish this revision</a>
                <a href="#">Duplicate and edit this revision</a>
            </p>
        </div>
<?php } ?>
        <ul>
<?php foreach ($widgets as $widgetKey => $widget) { ?>
            <li>
                <a href="#">
                    <span><?php echo htmlspecialchars($widget->getTitle()); ?></span>
                    <img id="ipAdminWidgetButton_<?php echo $widget->getName(); ?>"
                         class="ipAdminWidgetButton ipAdminWidgetButtonSelector"
                         alt="<?php echo htmlspecialchars($widget->getTitle()); ?>"
                         src="<?php echo BASE_URL.$widget->getIcon() ?>" />
                </a>
            </li>
<?php } ?>
        </ul>
    </div>

    <div class="ipAdminControls">
        <div class="ipgLeft">
            <span class="ipAdminControlsLabel"><?php echo $parametersMod->getValue('standard', 'content_management', 'admin_translations', 'man_additional_button_title'); ?></span>
            <input type="text" class="ipAdminInput" value="<?php echo htmlspecialchars('variable'); ?>" />
            <a href="#" class="ipAdminButton ipaOptions"><span><?php echo $parametersMod->getValue('standard', 'content_management', 'admin_translations','advanced'); ?></span></a>
        </div>
        <div class="ipgRight">
            <a href="#" class="ipAdminButton ipaSave ipActionSave">Save</a>
            <div class="ipAdminRevisions">
                <a href="#" class="ipAdminButton ipaDropdown"><span>&nbsp;</span></a>
                <ul>
<?php foreach ($revisions as $revisionKey => $revision){ ?>
                    <li<?php echo $revision['revisionId'] == $currentRevision['revisionId'] ? ' class="ipaActive" ' : '' ?>>
                        <a href="<?php echo $managementUrls[$revisionKey]; ?>">
                            <?php echo (int)$revision['revisionId'].' - '.date("Y-m-d H:i", $revision['created']); echo $revision['published'] ? ' (published) ' : ''; ?>
                        </a>
                    </li>
<?php } ?>
                </ul>
            </div>
            <a href="#" class="ipAdminButton ipaConfirm ipActionPublish">Publish</a>
        </div>
    </div>

</div>
