<?php
/**
 * @desc Generate language selection menu with custom ul id and class and custom li class
 */
?>

<ul <?php echo $attributesStr;?>>
    <?php foreach ($languages as $key => $language) { ?>
        <?php /** @var $language \Ip\Language */?>
        <?php if (!$language->isVisible()) { continue; }?>
        <?php $actClass = ($language->isCurrent()) ? ' class="current"' : ''; ?>
        <li <?php echo $li_class_prepend.$actClass ?>>
            <a title="<?php echo esc($language->getTitle()) ?>" href="<?php echo $language->getLink() ?>">
                <?php echo esc($language->getAbbreviation())?>
            </a>
        </li>
    <?php } ?>
</ul>
