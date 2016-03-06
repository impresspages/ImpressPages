<?php
/**
 * @desc Generate language selection menu with custom ul id and class and custom li class
 */
?>

<ul <?php echo $attributesStr;?>>
    <?php foreach ($languages as $key => $language) { ?>
        <?php /** @var $language \Ip\Language */?>
        <?php if (!$language->isVisible()) { continue; }?>
        <?php $activeClass = ($language->isCurrent()) ? ' class="active"' : ''; ?>
        <li <?php echo $activeClass; ?>>
            <a title="<?php echo escAttr($language->getTitle()); ?>" href="<?php echo escAttr($language->getLink()); ?>">
                <?php echo esc($language->getAbbreviation()); ?>
            </a>
        </li>
    <?php } ?>
</ul>
