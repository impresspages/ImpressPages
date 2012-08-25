<div class="ipaImageContainer">
    <div class="ipaOptions">
        <label class="ipAdminLabel"><?php echo htmlspecialchars($translations['title']) ?></label>
        <input type="text" class="ipAdminInput ipaImageTitle" name="title" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" />
    </div>
    <div class="ipaImage"></div>
</div>

<div class="ipaTextarea">
    <textarea name="text">
<?php echo isset($text) ? htmlentities($text, (ENT_COMPAT), 'UTF-8') : ''; ?>
    </textarea>
</div>