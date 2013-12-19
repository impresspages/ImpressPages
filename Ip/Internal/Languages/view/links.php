<?php foreach ($languages as $key => $language) { ?>
    <?php if($language['current']) { ?>
        <a title="<?php echo esc($language['longTitle']) ?>" class="act" href="<?php echo $language['url'] ?>"><?php echo esc($language['shortTitle']) ?></a>
    <?php } else { ?>
        <a title="<?php echo esc($language['longTitle']) ?>" href="<?php echo $language['url'] ?>"><?php echo esc($language['shortTitle']) ?></a>
    <?php } ?>
<?php } ?>