<label class="ipAdminLabel"><?php echo $this->escPar('standard/content_management/widget_title/title') ?><br/>
<input name="title" required="required" class="ipAdminInput" value="<?php echo htmlspecialchars(isset($title) ? $title : '' ); ?>" /></label>
<label class="ipAdminLabel">Id (optional, for relative links)<br/>
<input name="id" class="ipAdminInput" value="<?php echo htmlspecialchars(isset($id) ? $id : '' ); ?>" /></label>