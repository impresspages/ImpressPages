<label class="ipAdminLabel"><?php echo $this->escPar('standard/content_management/widget_title/title') ?><br/>
<input name="title" required="required" class="ipAdminInput" value="<?php echo htmlspecialchars(isset($title) ? $title : '' ); ?>" /></label>
<a href="#" class="ipAdminButton ipTitleOptionsButton">Options</a>
<div class="ipTitleOptions" style="display: none;">
    <label class="ipAdminLabel">Id (optional, for relative links)<br/>
    <input name="id" class="ipAdminInput" value="<?php echo htmlspecialchars(isset($id) ? $id : '' ); ?>" /></label>
</div>