<ul class="tabs">
    <?php foreach ($values as $tabKey => $value) { ?>
    <li>
        <a href="#ipInlineManagementStringTabs-<?php echo $value['languageId']; ?>"><?php echo htmlspecialchars($value['language']); ?></a>
    </li>
    <?php } ?>
</ul>
<?php foreach ($values as $tabKey => $value) { ?>
<div id="ipInlineManagementStringTabs-<?php echo $value['languageId']; ?>">
   <textarea data-languageid='<?php echo $value['languageId']; ?>'><?php echo htmlentities($value['text'], (ENT_COMPAT), 'UTF-8'); ?></textarea>
</div>
<?php } ?>
<a class="ipAdminButton ipsConfirm" href="#"><?php _e('Confirm', 'ipAdmin'); ?></a>
<a class="ipAdminButton ipsCancel" href="#"><?php _e('Cancel', 'ipAdmin'); ?></a>
