<div class="ip ipsAdminNavbarContainer">
    <div class="ipAdminNavbar ipsAdminNavbar navbar navbar-default navbar-fixed-top navbar-inverse" role="navigation">
        <button type="button" class="_toggle ipsAdminMenu navbar-toggle">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <div class="_menu ipsAdminMenuBlock">
            <div class="_menuHeader">
                <button type="button" class="_toggle navbar-toggle">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <p class="navbar-text"><?php _e('Menu', 'ipAdmin'); ?></p>
            </div>
            <div class="_menuContainer ipsAdminMenuBlockContainer">
                <nav>
                    <?php
                        $data = array(
                            'items' => $menuItems,
                            'depth' => 1
                        );
                        $view = ipView('menu.php', $data);
                        echo $view->render();
                    ?>
                    <ul class="nav nav-stacked">
                        <li>
                            <a href="<?php echo esc($helpUrl); ?>" target="_blank">
                                <i class="fa fa-fw fa-info"></i>
                                <?php _e('Help', 'ipAdmin'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo ipActionUrl(array('sa' => 'Admin.logout')); ?>">
                                <i class="fa fa-fw fa-power-off"></i>
                                <?php _e('Logout', 'ipAdmin'); ?>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <?php if ($curModTitle) { ?>
            <ul class="nav navbar-nav _active">
                <li class="ipsItemCurrent">
                    <a href="<?php echo esc($curModUrl); ?>">
                        <?php if ($curModIcon) { ?>
                            <i class="fa fa-fw <?php echo esc($curModIcon); ?>"></i>
                        <?php } ?>
                        <?php echo esc($curModTitle); ?>
                    </a>
                </li>
            </ul>
        <?php } ?>

        <ul class="nav navbar-nav navbar-right">
            <li>
                <a href="#" class="ipsAdminPreview">
                    <i class="fa fa-eye"></i>
                    <?php _e('Page preview', 'ipAdmin'); ?>
                </a>
            </li>
            <li>
                <a href="<?php echo ipActionUrl(array('sa' => 'Admin.logout')); ?>" class="ipsAdminLogout" title="<?php _e('Logout', 'ipAdmin'); ?>">
                    <i class="fa fa-power-off"></i>
                </a>
            </li>
        </ul>
    </div>
</div>
