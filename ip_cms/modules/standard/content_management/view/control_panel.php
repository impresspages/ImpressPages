<div id="ipControllPanel">
    <ul class="ipWidgetList">
    <?php foreach ($widgets as $widgetKey => $widget) { ?>
        <li id="ipWidgetButton_<?php echo $widget->getName(); ?>" class="ipWidgetButton ipWidgetButtonSelector">
            <img title="<?php echo htmlspecialchars($widget->getTitle()); ?>"  alt="<?php echo htmlspecialchars($widget->getTitle()); ?>" src="<?php echo BASE_URL.$widget->getIcon() ?>" />
        </li>
    <?php } ?>
    </ul>
    <div>
        <select>
            <?php foreach ($revisions as $revisionKey => $revision){ ?>
                <option ><?php echo (int)$revision['id'].' - '.date("Y-m-d H:i", $revision['created']); ?></option>
            <?php } ?>
        </select>
    </div>
    <div>
        <span class="ipPageSave">Save (create revision)</span>
        <span class="ipPagePublish">Publish</span>
    </div>
</div>
