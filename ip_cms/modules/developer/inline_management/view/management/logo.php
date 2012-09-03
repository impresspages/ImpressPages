<div class="logo ipModuleInlineManagementLogo" <?php echo empty($cssClass) ? '' : $cssClass ?> data-cssclass='<?php echo $this->esc($cssClass); ?>'>
<?php if (isset($type) && $type == 'image') { ?>
    <a href="<?php echo isset($link) ? $link : '' ?>" style="<?php echo !empty($color) ? 'color: '.htmlspecialchars($color).';' : '' ?> <?php echo !empty($font) ? 'font-family: '.htmlspecialchars($font).';' : '' ?>">
        <img src="<?php echo BASE_URL.($this->esc(!empty($image) ? $image : MODULE_DIR.'developer/inline_management/public/empty.gif')) ?>" alt="<?php echo $this->escPar('standard/configuration/main_parameters/name'); ?>" />
    </a>
<?php } else { ?>
    <a href="<?php echo isset($link) ? $link : '' ?>" style="<?php echo !empty($color) ? 'color: '.htmlspecialchars($color).';' : '' ?> <?php echo !empty($font) ? 'font-family: '.htmlspecialchars($font).';' : '' ?>">
        <?php echo nl2br($this->esc(isset($text) ? $text : '')) ?>
    </a>
<?php } ?>
</div>
