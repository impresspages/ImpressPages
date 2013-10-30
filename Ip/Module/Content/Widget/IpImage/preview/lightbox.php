<?php if (isset($imageSmall) && $imageSmall != ''){
    $lightboxImage = $imageOriginal; // Original
    //$lightboxImage = $imageBig; // Big (cropped by parameters)
?>
<a href="<?php echo \Ip\Config::baseUrl($lightboxImage) ?>" title="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>">
    <img src="<?php echo \Ip\Config::baseUrl($imageSmall) ?>" alt="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" />
</a>
<?php } ?>
