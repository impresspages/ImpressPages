<div id="ipControllPanel">
    <ul class="ipWidgetList ipWidgetsSelector">
    <?php foreach ($widgets as $widgetKey => $widget) { ?>
        <li class="ipWidget ipWidgetAddSelector">
            <img title="<?php echo htmlspecialchars($widget->getTitle()); ?>"  alt="<?php echo htmlspecialchars($widget->getTitle()); ?>" src="<?php echo BASE_URL.$widget->getIcon() ?>" />
        </li>
    <?php } ?>
    </ul>
</div>