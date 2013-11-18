<label class="ipAdminLabel"><?php echo _esc('Title', 'ipAdmin') ?><br/>
<input name="title" required="required" class="ipAdminInput" value="<?php echo htmlspecialchars(isset($title) ? $title : '' ); ?>" /></label>
<a href="#" class="ipAdminButton ipsTitleOptionsButton">Options</a>
<div class="ipsTitleOptions" style="display: none;">
    <label class="ipAdminLabel"><?php echo _esc('Anchor', 'ipAdmin'); ?> <span class="ipsAnchorPreview ipmAnchorPreview"><?php echo $curUrl ?>#<?php echo htmlspecialchars(isset($id) ? $id : '' ); ?></span><br/>
    <input name="id" class="ipAdminInput ipsAnchor" value="<?php echo htmlspecialchars(isset($id) ? $id : '' ); ?>" /></label>
</div>