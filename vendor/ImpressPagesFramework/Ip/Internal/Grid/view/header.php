<?php
    if (!empty($breadcrumb) && is_array($breadcrumb)) {
?>
    <ol class="breadcrumb">
        <?php foreach($breadcrumb as $key => $crumb) { ?>
            <li><a href="<?php echo escAttr($crumb['url']); ?>"><?php echo esc($crumb['title']); ?></a></li>
        <?php } ?>
    </ol>
<?php } ?>
<?php if($title){ ?>
    <h1><?php echo esc($title); ?></h1>
<?php } ?>
