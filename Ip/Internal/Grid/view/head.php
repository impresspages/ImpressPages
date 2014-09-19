<?php
    $lastCrumb = array_pop($breadcrumb);
    if (!empty($breadcrumb) && is_array($breadcrumb)) {
?>
    <ol class="breadcrumb">
        <?php foreach($breadcrumb as $key => $crumb) { ?>
            <li><a href="<?php echo escAttr($crumb['url']); ?>"><?php echo esc($crumb['title']); ?></a></li>
        <?php } ?>
        <li class="active"><?php echo esc($lastCrumb['title']); ?></li>
    </ol>
<?php } ?>
<h1><?php echo esc($title); ?></h1>
