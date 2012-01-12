<a href="<?php echo $homeUrl; ?>"><?php echo $this->escPar('standard/configuration/translations/home') ?></a>
<?php foreach ($breadcrumbElements as $key => $element) { ?>
    &gt;
    <a href="<?php echo $element->getLink() ?>" title="<?php echo htmlspecialchars($element->getPageTitle()) ?>"><?php echo htmlspecialchars($element->getButtonTitle()) ?></a>
<?php } ?>
