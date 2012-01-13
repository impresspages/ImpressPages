<?php if (isset($imageSmall) && $imageSmall != ''){
    $lightboxImage = $imageOriginal; // Original
    //$lightboxImage = $imageBig; // Big (cropped by parameters)
?>
<a href="<?php echo BASE_URL.$lightboxImage; ?>" title="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>">
    <img src="<?php echo BASE_URL.$imageSmall; ?>" alt="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" />
</a>
<?php } ?>
