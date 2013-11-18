<h1><?php _e('Choose interface language', 'ipInstall') ?></h1>

<?php foreach ($languages as $key => $language) { ?>
    <?php if ($key == $selectedLanguage) { ?>
        <a class="selected" href="index.php?step=2&lang='<?php echo htmlspecialchars($key) ?>"><?php echo htmlspecialchars($language) ?></a><br/>
    <?php } else { ?>
        <a href="index.php?step=2&lang=<?php echo htmlspecialchars($key) ?>"><?php echo htmlspecialchars($language) ?></a><br/>
    <?php } ?>
<?php } ?>
<br/><br/>

<a class="button_act" href="?step=1"><?php _e('Next', 'ipInstall') ?></a>