<div class="ipAdminPanel">
    <div class="ipAdminWidgets">
<?php if(!$manageableRevision){ ?>
        <div class="ipAdminWidgetsDisable">
            <p>
                <?php echo $this->escPar('standard/content_management/admin_translations/older_revision_preview', array ('date' => date("Y-m-d H:i", $currentRevision['created']))) ?>
                <a href="#" class="ipActionPublish"><?php echo $this->escPar('standard/content_management/admin_translations/older_revision_publish') ?></a>
                <a href="#" class="ipActionSave"><?php echo $this->escPar('standard/content_management/admin_translations/older_revision_edit') ?></a>
            </p>
        </div>
<?php } ?>
        <a href="#" class="ipAdminWidgetsScroll ipaLeft"></a>
        <a href="#" class="ipAdminWidgetsScroll ipaRight"></a>
        <div class="ipAdminWidgetsContainer">
<?php $scrollWidth = count($widgets)*75; // to keep all elements on one line ?>
            <ul<?php echo ' style="width: '.$scrollWidth.'px;"'; ?>>
<?php foreach ($widgets as $widgetKey => $widget) { ?>
                <li>
                    <div id="ipAdminWidgetButton-<?php echo $widget->getName(); ?>" class="ipActionWidgetButton">
                        <a href="#">
                            <span><?php echo htmlspecialchars($widget->getTitle()); ?></span>
                            <img src="<?php echo BASE_URL.$widget->getIcon() ?>" alt="<?php echo htmlspecialchars($widget->getTitle()); ?>" />
                        </a>
                    </div>
                </li>
<?php } ?>
            </ul>
        </div>
    </div>

    <div class="ipAdminControls">
        <div class="ipgLeft">
            <span class="ipaLabel"><?php echo $this->escPar('standard/content_management/admin_translations/man_additional_button_title'); ?></span>
            <input type="text" class="ipAdminInput ipaPageOptionsTitle" value="<?php echo $this->esc($page->getButtonTitle()); ?>" />
            <a href="#" class="ipAdminButton ipaOptions"><span><?php echo $this->escPar('standard/content_management/admin_translations/advanced') ?></span></a>
        </div>
        <div class="ipgRight">
            <a href="#" class="ipAdminButton ipaSave ipActionSave"><?php echo $this->escPar('standard/content_management/admin_translations/save_now') ?></a>
            <div class="ipAdminRevisions">
                <a href="#" class="ipAdminButton ipaDropdown"><span>&nbsp;</span></a>
                <ul>
<?php foreach ($revisions as $revisionKey => $revision){ ?>
                    <li<?php echo $revision['revisionId'] == $currentRevision['revisionId'] ? ' class="ipaActive" ' : '' ?>>
                        <a href="<?php echo $managementUrls[$revisionKey]; ?>">
                            <?php echo (int)$revision['revisionId'].' - '.date("Y-m-d H:i", $revision['created']); echo $revision['published'] ? ' '.$this->escPar('standard/content_management/admin_translations/published_revision_marker') . ' ' : ''; ?>
                        </a>
                    </li>
<?php } ?>
                </ul>
            </div>
            <a href="#" class="ipAdminButton ipaConfirm ipActionPublish"><?php echo $this->escPar('standard/content_management/admin_translations/publish') ?></a>
        </div>
    </div>
    <div class="ipAdminErrorContainer">
    </div>
    <div class="ipAdminErrorSample">
        <p class="ipAdminError"></p>
    </div>
</div>
