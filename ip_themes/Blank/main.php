<?php if (!defined('CMS')) exit; ?>
<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php echo $this->subview('_header.php'); ?>
        <div class="sidenav col_12 col_md_12 col_lg_3 left">
            <nav>
                <?php
                    // generate 2 - 7 levels submenu of top menu.
                    // please note that it is possible to generate second level only if first level item is selected
                    $pages = \Ip\Menu\Helper::getZoneItems('menu1', 2, 7);
                    echo $this->generateMenu('left', $pages);
                ?>
            </nav>
        </div>
        <div class="main col_12 col_md_12 col_lg_8 right">
            <?php echo $site->generateBlock('main'); ?>
        </div>
        <div class="side col_12 col_md_12 col_lg_3 left">
            <aside>
                <?php echo $this->generateBlock('side1', true); ?>
            </aside>
        </div>
        <div class="clear"></div>
<?php echo $this->subview('_footer.php'); ?>
