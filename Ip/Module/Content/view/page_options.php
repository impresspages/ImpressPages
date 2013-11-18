<ul class="tabs">
<?php foreach ($tabs as $tabKey => $tab) { ?>
    <li>
        <a href="#propertiesTabs-<?php echo ($tabKey + 1) ?>"><?php echo htmlspecialchars($tab['title']) ?></a>
    </li>
<?php } ?>
</ul>
<?php foreach ($tabs as $tabKey => $tab) { ?>
<div id="propertiesTabs-<?php echo ($tabKey + 1) ?>">
    <?php echo $tab['content'] ?>
</div>
<?php } ?>
<a class="ipAdminButton ipaOptionsConfirm ipaConfirm" href="#"><?php _e('Confirm', 'ipAdmin') ?></a>
<a class="ipAdminButton ipaOptionsCancel ipaCancel" href="#"><?php _e('Cancel', 'ipAdmin') ?></a>