<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $items \Ip\Menu\Item[]
 * @var $this \Ip\View
 */
?>
<?php if (isset($items[0])){?>
    <?php $firstItem = $items[0]; ?>
    <ul class="level<?php echo (int)$depth ?> <?php echo $depth == 1 ? 'nav nav-list' : '' ?>">
        <?php foreach($items as $item){ ?>
            <?php echo ipView('Ip/Internal/Admin/view/bootstrapNavItem.php', array('menuItem' => $item, 'depth' => $depth))->render(); ?>
        <?php } ?>
    </ul>
<?php } ?>
