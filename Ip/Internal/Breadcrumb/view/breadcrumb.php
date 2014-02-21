<?php if ($homeUrl) { ?>
    <a href="<?php echo $homeUrl; ?>"><?php _e('Home', 'ipPublic') ?></a>
    <?php echo $separator; ?>
<?php } ?>
<?php foreach ($breadcrumbElements as $key => $element) { ?>
    <a href="<?php echo esc($element->getLink(), 'attr'); ?>" title="<?php echo esc($element->getPageTitle(), 'attr'); ?>"><?php echo esc($element->getNavigationTitle()); ?></a>
    <?php echo $key < count($breadcrumbElements) -1 ? $separator : ''; ?>
<?php } ?>
