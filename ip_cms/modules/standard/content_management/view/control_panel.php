<div class="ipAdminPanel ipAdmin">
<?php if($manageableRevision){ ?>
    <div class="ipAdminWidgets">
        <ul>
    <?php foreach ($widgets as $widgetKey => $widget) { ?>
            <li id="ipAdminWidgetButton_<?php echo $widget->getName(); ?>"
                class="ipAdminWidgetButton ipAdminWidgetButtonSelector">
                <a href="#">
                    <span><?php echo htmlspecialchars($widget->getTitle()); ?></span>
                    <img alt="<?php echo htmlspecialchars($widget->getTitle()); ?>"
                         src="<?php echo BASE_URL.$widget->getIcon() ?>" />
                </a>
            </li>
            <?php } ?>
        </ul>
    </div>
    <?php } else { ?>
    <div>
        <p>
            This is a preview of older revision, created at (
            <?php echo date("Y-m-d H:i", $currentRevision['created']) ?>
            ).
        </p>
        <p>
            <a>Publish this revision</a>
        </p>
        <p>
            <a>Duplicate and edit this revision</a>
        </p>
    </div>

    <?php } ?>
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
            <a href="#" class="ipAdminButton ipaPublish ipActionPublish">Publish</a>
        </div>
    </div>
</div>
