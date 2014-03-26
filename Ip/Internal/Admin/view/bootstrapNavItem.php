<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $menuItem \Ip\Menu\Item
 * @var $this \Ip\View
 */
?>

<?php
$css = array();
if($menuItem->isCurrent()) {
    $css[] = 'current';
    $css[] = 'active';
    $selected = true;
} elseif ($menuItem->isInCurrentBreadcrumb()) {
    $css[] = 'selected';
    $selected = true;
}

if(sizeof($menuItem->getChildren()) > 0) {
    $css[] = 'subnodes';
}

if ($menuItem->isDisabled()) {
    $css[] = 'disabled';
}

?>

<li class="<?php echo implode(' ', $css) ?>">
    <?php if ($href) { ?><a <?php echo escAttr($href) ?> title="<?php esc($menuItem->getTitle()) ?>"><?php } ?>
        <?php echo esc($menuItem->getTitle()) ?>
        <?php if ($href) { ?></a><?php } ?>
    <?php if ($menuItem->getChildren()){ ?>
        <?php echo ipView('Ip/Internal/Admin/view/bootstrapNav.php', array('items' => $menuItem->getChildren(), 'depth' => $depth + 1))->render(); ?>
    <?php } ?>
</li>
