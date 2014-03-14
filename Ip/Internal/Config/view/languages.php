<ul>
    <?php foreach ($languages as $key => $language) { ?>
        <?php /** @var $language \Ip\Language */?>
        <?php if (!$language->isVisible()) { continue; }?>
        <?php $actClass = ($language->isCurrent()) ? ' class="current"' : ''; ?>
        <li <?php echo $actClass ?>>
            <a title="<?php echo esc($language->getTitle()) ?>" href="<?php echo $language->getLink() ?>">
                <?php echo esc($language->getAbbreviation())?>
            </a>
        </li>
    <?php } ?>
</ul>
