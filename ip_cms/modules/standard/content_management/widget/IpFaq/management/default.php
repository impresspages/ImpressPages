<div class="ipaOptions">
    <label class="ipAdminLabel"><?php echo $this->escPar('standard/content_management/widget_faq/question') ?></label>
    <input name="question" class="ipAdminInput" value="<?php echo htmlspecialchars(isset($question) ? $question : '' ); ?>" />
    <br /><br />
    <label class="ipAdminLabel"><?php echo $this->escPar('standard/content_management/widget_faq/answer') ?></label>
    <textarea name="answer" class="ipAdminTextarea"><?php echo isset($answer) ? $answer : ''; ?></textarea>
</div>