<ul>
    <?php foreach ($languages as $key => $language) { ?>
        <?php $actClass = ($language['current']) ? ' class="current"' : ''; ?>
        <li <?php echo $actClass ?>>
            <a title="<?php echo ipEsc($language['longTitle']) ?>" href="<?php echo $language['url'] ?>">
                <?php echo ipEsc($language['shortTitle'])?>
            </a>
        </li>
    <?php } ?>
</ul>
