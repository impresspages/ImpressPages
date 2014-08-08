<?php
/**
 * @var $pages \Ip\Page[]
 */
?>
<ol class="breadcrumb">
<?php if ($homeUrl) { ?>
    <li><a href="<?php echo $homeUrl; ?>"><?php _e('Home', 'Ip'); ?></a></li>
<?php } ?>
<?php foreach ($pages as $key => $page) { ?>
    <?php if ($key < count($pages) - 1) { ?>
        <li><a href="<?php echo escAttr($page->getLink()); ?>" title="<?php echo escAttr($page->getTitle()); ?>"><?php echo esc($page->getTitle()); ?></a></li>
    <?php } else { ?>
        <li class="active"><?php echo esc($page->getTitle()); ?></li>
    <?php } ?>
<?php } ?>
</ol>
