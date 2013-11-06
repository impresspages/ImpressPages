<div class="ipsContent" >
    <ul class="tabs">
        <?php foreach ($tabs as $tabKey => $tab) { ?>
            <li>
                <a href="#propertiesTabs-<?php echo ($tabKey + 1) ?>"><?php echo $this->esc($tab['title']) ?></a>
            </li>
        <?php } ?>
    </ul>
    <?php foreach ($tabs as $tabKey => $tab) { ?>
        <div id="propertiesTabs-<?php echo ($tabKey + 1) ?>">
            <?php echo $tab['content'] ?>
        </div>
    <?php } ?>
</div>
<div class="ipgHide ipmLoading ipsLoading"></div>
