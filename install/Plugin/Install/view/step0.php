<h1><?php _e('Choose interface language', 'Install'); ?></h1>

<?php foreach ($languages as $key => $language) { ?>
    <a href="index.php?step=1&lang=<?php echo htmlspecialchars($key) ?>">
        <?php echo htmlspecialchars($language) ?>
    </a>
    <?php if ($key == $selectedLanguage) { ?><span class="label label-info"><?php _e('Selected', 'Install'); ?></span><?php } ?>

    <br/>
<?php } ?>
<p>&nbsp;</p>
<p class="text-right">
    <a class="btn btn-primary" href="?step=1"><?php _e('Next', 'Install') ?></a>
</p>
