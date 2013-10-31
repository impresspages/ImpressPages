<div class="ipaQuestion">
    <label class="ipAdminLabel"><?php echo $this->escPar('standard/content_management/widget_faq/question') ?></label>
    <input name="question" class="ipAdminInput" value="<?php echo htmlspecialchars(isset($question) ? $question : '' ); ?>" />
</div>
<div class="ipaAnswer">
    <label class="ipAdminLabel"><?php echo $this->escPar('standard/content_management/widget_faq/answer') ?></label>
    <textarea name="answer" class="ipAdminTextarea"><?php echo isset($answer) ? htmlentities($answer, (ENT_COMPAT), 'UTF-8') : ''; ?></textarea>
</div>
