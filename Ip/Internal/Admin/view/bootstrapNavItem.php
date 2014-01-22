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

$css[] = 'type'.ucwords($menuItem->getType());

if ($menuItem->getType() != 'inactive' && $menuItem->getUrl()) {
    $href = 'href="'.$menuItem->getUrl().'"';
} else {
    $css[] = 'typeHeader';
    $href = '';
}
?>

<li class="<?php echo implode(' ', $css) ?>">
    <?php if ($href) { ?><a <?php echo $href ?> title="<?php addslashes($menuItem->getPageTitle()) ?>"><?php } ?>
        <?php echo esc($menuItem->getTitle()) ?>
        <?php if ($href) { ?></a><?php } ?>
    <?php if ($menuItem->getChildren()){ ?>
        <?php echo ipView('Ip/Internal/Admin/view/bootstrapNav.php', array('items' => $menuItem->getChildren(), 'depth' => $depth + 1))->render(); ?>
    <?php } ?>
</li>
