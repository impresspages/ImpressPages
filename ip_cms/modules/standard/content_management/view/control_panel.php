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
            
        </div>
        <div class="ipgRight">
            <a href="#" class="ipAdminButton">Save</a>
            <a href="#" class="ipAdminButton ipaPublish">Publish</a>
        </div>
    </div>
    <div class="ipRevisionsControl">
        <div>
            <select id="ipRevisionSelect">
            <?php foreach ($revisions as $revisionKey => $revision){ ?>
                <option
                <?php echo $revision['revisionId'] == $currentRevision['revisionId'] ? ' selected="selected" ' : '' ?>
                    value="<?php echo $managementUrls[$revisionKey]; ?>">
                    <?php echo (int)$revision['revisionId'].' - '.date("Y-m-d H:i", $revision['created']); echo $revision['published'] ? ' (published) ' : ''; ?>
                </option>
                <?php } ?>
            </select>
        </div>
        <div>
            <span class="ipPageSave">Save (create revision)</span>
        </div>
        <div>
            <span class="ipPagePublish">Publish</span>
        </div>
    </div>
</div>
