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
    $css[] = 'selected';
    $selected = true;
}

if(sizeof($menuItem->getChildren()) > 0) {
    $css[] = 'dropdown';
}

$css[] = 'type'.ucwords($menuItem->getType());

if ($menuItem->getType() != 'inactive' && $menuItem->getUrl()) {
    $href = 'href="'.$menuItem->getUrl().'"';
} else {
    $css[] = 'disabled';
    $href = '';
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
