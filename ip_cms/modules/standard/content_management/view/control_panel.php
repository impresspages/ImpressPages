<div id="ipControllPanel">
    <ul class="ipWidgetList ipWidgetsSelector">
    <?php foreach ($widgets as $widgetKey => $widget) { ?>
        <li id="ipWidgetButton_<?php echo $widget->getName(); ?>" class="ipWidgetButton ipWidgetButtonSelector">
            <img title="<?php echo htmlspecialchars($widget->getTitle()); ?>"  alt="<?php echo htmlspecialchars($widget->getTitle()); ?>" src="<?php echo BASE_URL.$widget->getIcon() ?>" />
        </li>
    <?php } ?>
    </ul>
</div>
