<div class="ipAdminToolbarContainer ipsAdminToolbarContainer">
    <div class="ipAdminToolbar ipsAdminToolbar">
        <a href="#" class="ipmItemMenu ipsAdminMenu">
            <span class="ipmMenuBar"></span>
            <span class="ipmMenuBar"></span>
            <span class="ipmMenuBar"></span>
        </a>
        <div class="ipsAdminMenuBlock ipmMenu">
            <span class="ipmMenuTitle">{{Menu}}</span>
            <?php
                echo $this->generateMenu('admin_navigation', $menuItems, 'bootstrapNav.php');
                //TODOX in this way anyone who can access menu config, can change this menu to anything :| secure somehow
            ?>
            <ul>
                <li>
                    <a href="<?php echo BASE_URL ?>?sa=Admin.logout">
                        <?php echo $this->escPar('standard/configuration/system_translations/logout') ?>
                        <span class="icon-stack">
                            <i class="icon-sign-blank icon-stack-base"></i>
                            <i class="icon-remove icon-light"></i>
                        </span>
                    </a>
                </li>
            </ul>
        </div>
        <a href="#" class="ipmItemCurrent">{{Newsletter Subscribers}}</a>
        <a href="<?php echo BASE_URL ?>?sa=Admin.logout" class="ipmItemLogout">
            <?php echo $this->escPar('standard/configuration/system_translations/logout') ?>
            <span class="icon-stack">
                <i class="icon-sign-blank icon-stack-base"></i>
                <i class="icon-remove icon-light"></i>
            </span>
        </a>
    </div>
</div>
