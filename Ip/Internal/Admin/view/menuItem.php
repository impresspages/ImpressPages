<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $menuItem \Ip\Menu\Item
 * @var $this \Ip\View
 */

$css = array();
if($menuItem->isCurrent()) {
    $css[] = 'active';
    $selected = true;
} elseif ($menuItem->isInCurrentBreadcrumb()) {
    $css[] = 'inBreadcrumb';
    $selected = true;
}

if(sizeof($menuItem->getChildren()) > 0) {
    $css[] = 'dropdown';
}

if ($menuItem->isDisabled()) {
    $css[] = 'disabled';
}


if ($menuItem->isDisabled()) {
    $href = '';
    $css[] = 'disabled';
} else {
    $href = 'href="' . esc($menuItem->getUrl(), 'attr') . '"';
}



?><li class="<?php echo implode(' ', $css); ?>">
    <a <?php echo $href ?> title="<?php echo esc($menuItem->getTitle(), 'attr'); ?>">
        <i class="fa fa-fw <?php echo esc($menuItem->getIcon()); ?>"></i>
        <?php echo esc($menuItem->getTitle()); ?>
    </a>
    <?php if ($menuItem->getChildren()) { ?>
        <?php echo ipView('menu.php', array('items' => $menuItem->getChildren(), 'depth' => $depth + 1))->render(); ?>
    <?php } ?>
</li>
