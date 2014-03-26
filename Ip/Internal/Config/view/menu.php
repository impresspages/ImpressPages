<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $items \Ip\Menu\Item[]
 * @var $this \Ip\View
 */
?>
<?php if (!empty($items)){ ?>
<ul <?php echo $attributesStr; ?>><?php
        foreach($items as $menuItem) {
            $css = array();
            if($menuItem->isCurrent()) {
                $css[] = $activeClass;
            } elseif ($menuItem->isInCurrentBreadcrumb()) {
                $css[] = $breadcrumbClass;
            }
            if(sizeof($menuItem->getChildren()) > 0) {
                $css[] = $parentClass;
            }
            if ($menuItem->isDisabled()) {
                $href = '';
                $css[] = $disabledClass;
            } else {
                $href = 'href="' . escAttr($menuItem->getUrl()) . '"';
            }
            if ($menuItem->getBlank()) {
                $target = 'target="_blank"';
            } else {
                $target = '';
            }
            if ($menuItem->getChildren()) {
                $submenuData = array(
                    'items' => $menuItem->getChildren(),
                    'depth' => $depth + 1,
                    'attributesStr' => 'class="level'.($depth+1).'"'
                );
                $submenuData = array_merge($this->getVariables, $submenuData);
                $submenu = ipView('menu.php', $submenuData)->render();
            } else {
                $submenu = '';
            }

            ?>
            <li class="<?php echo implode(' ', $css); ?>">
                <a <?php echo $href ?> <?php echo $target ?> title="<?php echo escAttr($menuItem->getTitle()); ?>">
                    <?php echo esc($menuItem->getTitle()); ?>
                </a>
                <?php echo $submenu; ?>
            </li><?php
        } ?>
</ul>
<?php }
