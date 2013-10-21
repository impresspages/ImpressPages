<?php if (isset($logos) && is_array($logos)){ ?>
<ul>
<?php
foreach ($logos as $logoKey => $logo) { 
    $curLogo = isset($logo['logoSmall']) ? $logo['logoSmall'] : '';
    $curTitle = isset($logo['title']) ? $logo['title'] : '';
    $curLink = isset($logo['link']) ? $logo['link'] : '';
?>
    <li>
        <?php if ($curLink ) { ?>
        <a href="<?php echo htmlspecialchars($curLink); ?>" title="<?php echo htmlspecialchars($curTitle); ?>" <?php if ($this->getDoctype() != \Ip\View::DOCTYPE_HTML4_STRICT && stripos($curLink, '#') !== 0) { ?>target="_blank"<?php } ?>>
            <img src="<?php echo htmlspecialchars(BASE_URL.$curLogo); ?>" alt="<?php echo htmlspecialchars($curTitle); ?>" />
        </a>
        <?php } else { ?>
        <img src="<?php echo htmlspecialchars(BASE_URL.$curLogo); ?>" alt="<?php echo htmlspecialchars($curTitle); ?>" />
        <?php } ?>
    </li>
<?php } ?>
</ul>
<?php } ?>
