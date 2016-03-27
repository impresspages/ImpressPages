<?php // @Layout name: Main ?>
<?php echo ipView('_header.php')->render(); ?>
    <div class="sidenav col_12 col_md_12 col_lg_3 left">
        <nav<?php if (ipGetThemeOption('collapseSidebarMenu') == 'yes') { echo ' class="collapse"'; }?>>
            <?php
                // generate 2 - 7 levels submenu
                // please note that it is possible to generate second level only if first level item is in breadcrumb
                // $pages = \Ip\Menu\Helper::getMenuItems('menu1', 2, 7);
                // echo ipSlot('menu', $pages);

                 //submenu of currently active menu item
                 //$pages = \Ip\Menu\Helper::getChildItems();
                 //echo ipSlot('menu', $pages);

                echo ipSlot('menu', 'menu2');
            ?>
        </nav>
    </div>
    <div class="main col_12 col_md_12 col_lg_8 right">
        <?php echo ipSlot('breadcrumb'); ?>
        <?php echo ipBlock('main')->render(); ?>
    </div>
    <div class="side col_12 col_md_12 col_lg_3 left">
        <aside>
            <?php echo ipBlock('side1')->asStatic()->render(); ?>
        </aside>
    </div>
    <div class="clear"></div>
<?php echo ipView('_footer.php')->render(); ?>
