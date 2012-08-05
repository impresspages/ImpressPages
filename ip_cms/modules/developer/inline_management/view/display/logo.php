<?php if (isset($type) && $type == 'image') { ?>


<?php } else { ?>
    <a href="<?php echo isset($link) ? $link : '' ?>" class="sitename" style="<?php echo !empty($color) ? 'color: '.htmlspecialchars($color).';' : '' ?> <?php echo !empty($font) ? 'font-family: '.htmlspecialchars($font).';' : '' ?>">
        <?php echo nl2br($this->esc(isset($text) ? $text : '')) ?>
    </a>
<?php } ?>
