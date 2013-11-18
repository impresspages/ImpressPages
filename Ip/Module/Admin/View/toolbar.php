<div class="ipAdminToolbarContainer ipsAdminToolbarContainer">
    <div class="ipAdminToolbar ipsAdminToolbar">
        <a href="#" class="ipmItemMenu ipsAdminMenu">
            <span class="ipmMenuBar"></span>
            <span class="ipmMenuBar"></span>
            <span class="ipmMenuBar"></span>
        </a>
        <div class="ipsAdminMenuBlock ipmMenu">
            <a class="ipmItemMenu">
                <span class="ipmMenuBar"></span>
                <span class="ipmMenuBar"></span>
                <span class="ipmMenuBar"></span>
            </a>

            <span class="ipmMenuTitle"><?php _e('Menu', 'ipAdmin') ?></span>
            <?php
                echo $this->generateMenu('admin_navigation', $menuItems, 'bootstrapNav.php');
                //TODOX in this way anyone who can access menu config, can change this menu to anything :| secure somehow
            ?>
            <ul>
                <li>
                    <a href="<?php echo \Ip\Config::baseUrl('', array('sa' => 'Admin.logout')) ?>">
                        <?php echo $this->esc(__('Logout', 'ipAdmin')) ?>
                        <span class="icon-stack">
                            <i class="icon-sign-blank icon-stack-base"></i>
                            <i class="icon-remove icon-light"></i>
                        </span>
                    </a>
                </li>
            </ul>
        </div>
        <?php if ($curModTitle) { ?>
            <a href="<?php echo $this->esc($curModUrl) ?>" class="ipmItemCurrent ipsItemCurrent ipmMobileHide"><?php echo $this->esc($curModTitle) ?></a>
        <?php } ?>



        <a href="<?php echo \Ip\Config::baseUrl('', array('sa' => 'Admin.logout')) ?>" class="ipmAdminAction ipmMobileHide">
            <?php echo $this->esc(__('Logout', 'ipAdmin')) ?>
            <span class="icon-stack">
                <i class="icon-sign-blank icon-stack-base"></i>
                <i class="icon-remove icon-light"></i>
            </span>
        </a>
        <a target="_blank" href="<?php echo $this->esc($helpUrl); ?>" class="ipmAdminAction ipmMobileHide">
            <?php echo $this->esc(__('Help', 'ipAdmin')) ?>
            <span class="icon-stack">
                <i class="icon-sign-blank icon-stack-base"></i>
                <i class="icon-question icon-light"></i>
            </span>
        </a>
    </div>
</div>
