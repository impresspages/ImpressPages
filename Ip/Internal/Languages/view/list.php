<ul>
    <?php foreach ($languages as $key => $language) { ?>
        <?php $actClass = ($language['current']) ? ' class="current"' : ''; ?>
        <li <?php echo $actClass ?>>
            <a title="<?php echo esc($language['longTitle']) ?>" href="<?php echo $language['url'] ?>">
                <?php echo esc($language['shortTitle'])?>
            </a>
        </li>
    <?php } ?>
</ul>
