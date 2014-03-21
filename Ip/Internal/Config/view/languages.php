<?php
/**
	 * @desc Generate language selection menu with custom ul id and class and custom li class
	 * @author Allan Laal <allan@permanent.ee>
	 * @param array $attributes
	 * @example 
			echo \Ip\Internal\Core\Slot::languages_80(array(
				'ul' => array(
					'id'	=> 'langmenu',
					'class'	=> 'floatlist right clearfix',
				),
				'li' => array(
					'class'			=> '', // will be prepended
				)
			));
	 * @return string
	 */
$ul_id = '';
if (isset($attributes['ul']['id']))
{
	$ul_id = 'id="'.$attributes['ul']['id'].'"';
}

$ul_class = '';
if (isset($attributes['ul']['class']))
{
	$ul_class = 'class="'.$attributes['ul']['class'].'"';
}

$li_class_prepend = '';
if (isset($attributes['li']['class']))
{
	$li_class_prepend = $attributes['li']['class'].' ';
}
?>
<ul <?php echo $ul_id;?> <?php echo $ul_class;?>>
    <?php foreach ($languages as $key => $language) { ?>
        <?php /** @var $language \Ip\Language */?>
        <?php if (!$language->isVisible()) { continue; }?>
        <?php $actClass = ($language->isCurrent()) ? ' class="current"' : ''; ?>
        <li <?php echo $li_class_prepend.$actClass ?>>
            <a title="<?php echo esc($language->getTitle()) ?>" href="<?php echo $language->getLink() ?>">
                <?php echo esc($language->getAbbreviation())?>
            </a>
        </li>
    <?php } ?>
</ul>
