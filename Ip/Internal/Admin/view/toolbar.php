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

                $viewFile = ipFile('Ip/Internal/Config/view/menu.php');
                $data = array(
                    'items' => $menuItems,
                    'depth' => 1
                );
                $view = ipView($viewFile, $data);
                echo $view->render();
            ?>
            <ul>
                <li>
                    <a href="<?php echo ipActionUrl(array('sa' => 'Admin.logout')) ?>">
                        <?php _e('Logout', 'ipAdmin') ?>
                        <span class="fa-stack">
                            <i class="fa fa-square fa-stack-2x"></i>
                            <i class="fa fa-times fa-stack-1x"></i>
                        </span>
                    </a>
                </li>
            </ul>
        </div>
        <?php if ($curModTitle) { ?>
            <a href="<?php echo esc($curModUrl) ?>" class="ipmItemCurrent ipsItemCurrent ipmMobileHide"><?php echo esc($curModTitle) ?></a>
        <?php } ?>



        <a href="<?php echo ipActionUrl(array('sa' => 'Admin.logout')) ?>" class="ipsAdminLogout ipmAdminAction ipmMobileHide">
            <?php _e('Logout', 'ipAdmin') ?>
            <span class="fa-stack">
                <i class="fa fa-square fa-stack-2x"></i>
                <i class="fa fa-times fa-stack-1x"></i>
            </span>
        </a>
        <a target="_blank" href="<?php echo esc($helpUrl); ?>" class="ipmAdminAction ipmMobileHide">
            <?php _e('Help', 'ipAdmin') ?>
            <span class="fa-stack">
                <i class="fa fa-square fa-stack-2x"></i>
                <i class="fa fa-question fa-stack-1x"></i>
            </span>
        </a>
    </div>
</div>
