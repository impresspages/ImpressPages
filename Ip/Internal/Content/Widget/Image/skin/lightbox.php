<?php if (isset($imageSmall) && $imageSmall != ''){
    $lightboxImage = $imageOriginal; // Original
    //$lightboxImage = $imageBig; // Big (cropped by parameters)
?>
<a href="<?php echo esc($lightboxImage) ?>" title="<?php echo isset($title) ? esc($title) : ''; ?>">
    <img src="<?php echo esc($imageSmall) ?>" alt="<?php echo isset($title) ? esc($title) : ''; ?>" />
</a>
<?php } ?>
