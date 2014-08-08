<?php if (!empty($breadcrumb) && is_array($breadcrumb)) { ?>
    <?php foreach($breadcrumb as $key => $crumb) { ?>
        <?php if ($key > 0) { ?>
            <h1> > </h1>
        <?php } ?>
        <a href="<?php echo escAttr($crumb['url']) ?>"><h1><?php echo esc($crumb['title']) ?></h1></a>
    <?php } ?>
<?php } ?>
