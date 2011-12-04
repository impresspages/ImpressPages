<div id="ipControllPanel">
<?php if($manageableRevision){ ?>
    <ul class="ipWidgetList">
    <?php foreach ($widgets as $widgetKey => $widget) { ?>
        <li id="ipWidgetButton_<?php echo $widget->getName(); ?>"
            class="ipWidgetButton ipWidgetButtonSelector"><img
            title="<?php echo htmlspecialchars($widget->getTitle()); ?>"
            alt="<?php echo htmlspecialchars($widget->getTitle()); ?>"
            src="<?php echo BASE_URL.$widget->getIcon() ?>" /></li>
            <?php } ?>
    </ul>
    <?php } else { ?>
    <div style="float: left;">
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
