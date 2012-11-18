<div class="ipAdminPanel">
    <div class="ipAdminControls">
        <div class="ipAdminWidgetsSearch clearfix">
            <div class="ipaControls">
                <span class="ipaArrow"></span>
                <input type="text" class="ipAdminInput ipaInput" value="<?php echo $this->escPar('standard/content_management/admin_translations/search_widgets') ?>" />
                <a href="#" class="ipaButton"></a>
            </div>
        </div>
        <a href="#" class="ipAdminButton ipaOptions" title="<?php echo $this->escPar('standard/content_management/admin_translations/advanced'); ?>"><span>&nbsp;</span></a>
        <a href="<?php echo str_replace('cms_action=manage', '', $site->generateCurrentUrl()); ?>" target="_blank" class="ipAdminButton ipaPreview ipActionPreview" title="<?php echo $this->escPar('standard/content_management/admin_translations/preview'); ?>"><span>&nbsp;</span></a>
        <a href="#" class="ipAdminButton ipaConfirm ipActionPublish" title="<?php echo $this->escPar('standard/content_management/admin_translations/publish'); ?>"><?php echo $this->escPar('standard/content_management/admin_translations/publish') ?></a>
        <div class="ipAdminRevisions">
            <a href="#" class="ipAdminButton ipaRevisions"><span>&nbsp;</span></a>
            <div class="ipaDropdownBlock">
                <a href="#" class="ipAdminButton ipaSave ipActionSave" title="<?php echo $this->escPar('standard/content_management/admin_translations/save_now_hint'); ?>"><?php echo $this->escPar('standard/content_management/admin_translations/save_now') ?></a>
                <ul>
<?php foreach ($revisions as $revisionKey => $revision){
          $revisionClass = '';
          if ($revision['revisionId'] == $currentRevision['revisionId']) {
              $revisionClass .= $revisionClass ? ' ' : '';
              $revisionClass .= 'ipaActive';
          }
          if ($revision['published']) {
              $revisionClass .= $revisionClass ? ' ' : '';
              $revisionClass .= 'ipaPublished';
          }
?>
                    <li<?php echo $revisionClass ? ' class="'.$revisionClass.'"' : ''; ?>>
                        <a href="<?php echo $managementUrls[$revisionKey]; ?>">
                            <strong><?php echo (int)$revision['revisionId'] ?></strong> - <?php echo date("Y-m-d H:i", $revision['created']); echo $revision['published'] ? ' '.$this->escPar('standard/content_management/admin_translations/published_revision_marker') . ' ' : ''; ?>
                        </a>
                    </li>
<?php } ?>
                </ul>
            </div>
        </div>
    </div>

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
<?php $scrollWidth = count($widgets)*87; // to keep all elements on one line ?>
            <ul<?php echo ' style="width: '.$scrollWidth.'px;"'; ?>>
<?php foreach ($widgets as $widgetKey => $widget) { ?>
                <li>
                    <div id="ipAdminWidgetButton-<?php echo $widget->getName(); ?>" class="ipActionWidgetButton">
                        <a href="#">
                            <span class="ipaTitle"><span><?php echo htmlspecialchars($widget->getTitle()); ?></span></span>
                            <img src="<?php echo BASE_URL.$widget->getIcon() ?>" alt="<?php echo htmlspecialchars($widget->getTitle()); ?>" />
                        </a>
                    </div>
                </li>
<?php } ?>
            </ul>
        </div>
    </div>
    <div class="ipAdminErrorContainer"></div>
    <div class="ipAdminErrorSample">
        <p class="ipAdminError"></p>
    </div>
</div>
