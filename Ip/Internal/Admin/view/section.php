<?php
/**
 * Level 2 navigation: the sections of the current level 1 group.
 *
 * @var array $group one group from \Ip\Internal\Admin\Navigation::groups()
 */
?>
<nav class="ipAdminSection ipsAdminSection" aria-labelledby="ipAdminSectionTitle">
    <h2 class="_title" id="ipAdminSectionTitle"><?php echo esc($group['title']); ?></h2>
    <ul class="_items">
        <?php foreach ($group['items'] as $item) { ?>
            <li>
                <a class="_link" href="<?php echo escAttr($item['url']); ?>"
                   data-hash="<?php echo escAttr((string)$item['hash']); ?>"
                   <?php echo $item['current'] ? ' aria-current="page"' : ''; ?>><?php echo esc($item['title']); ?></a>
            </li>
        <?php } ?>
    </ul>
</nav>
