<?php if (isset($logos) && is_array($logos)){ ?>
<ul>
<?php
foreach ($logos as $logoKey => $logo) { 
    $curLogo = isset($logo['logoSmall']) ? $logo['logoSmall'] : '';
    $curTitle = isset($logo['title']) ? $logo['title'] : '';
    $curLink = isset($logo['link']) ? $logo['link'] : '';
?>
    <li>
        <a href="<?php echo htmlspecialchars($curLink); ?>" title="<?php echo htmlspecialchars($curTitle); ?>">
            <img src="<?php echo htmlspecialchars(BASE_URL.$curLogo); ?>" alt="<?php htmlspecialchars($curTitle); ?>" />
        </a>
    </li>
<?php } ?>
</ul>
<?php } ?>