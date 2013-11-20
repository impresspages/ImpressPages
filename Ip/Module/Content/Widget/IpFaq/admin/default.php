<div class="ipaQuestion">
    <label class="ipAdminLabel"><?php _e('Question', 'ipAdmin') ?></label>
    <input name="question" class="ipAdminInput" value="<?php echo htmlspecialchars(isset($question) ? $question : '' ); ?>" />
</div>
<div class="ipaAnswer">
    <label class="ipAdminLabel"><?php _e('Answer', 'ipAdmin') ?></label>
    <textarea name="answer" class="ipAdminTextarea"><?php echo isset($answer) ? htmlentities($answer, (ENT_COMPAT), 'UTF-8') : ''; ?></textarea>
</div>
