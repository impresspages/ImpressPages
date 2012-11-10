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
<a class="ipAdminButton ipaOptionsConfirm ipaConfirm" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/confirm') ?></a>
<a class="ipAdminButton ipaOptionsCancel ipaCancel" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/cancel') ?></a>