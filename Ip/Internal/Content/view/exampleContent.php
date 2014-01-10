<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php echo ipRenderWidget('IpTitle', array('title' => 'This is an example title')); ?>

<?php echo ipRenderWidget('IpText', array('text' => '
    <p>Here is a text block where to can put any information. It supports <b>bold</b>, <em>italics</em>, <span style="text-decoration: underline;">underline</span>, <a href="http://www.impresspages.org">various links</a>. To make lists is really easy:</p>
    <ul>
        <li>Add widgets to any block by simply dragging and dropping;</li>
        <li>Paste any content to a text widget and it will adapt to your website\'s styles automatically;</li>
        <li>And many more.</li>
    </ul>
')); ?>
<?php echo ipRenderWidget('IpTitle', array('title' => 'For titles always use "Title" widget'), 'level2'); ?>

<?php echo ipRenderWidget('IpImage', array('imageSmall' => 'Ip/Internal/Content/img/example_image.jpg')); ?>

<?php echo ipRenderWidget('IpText', array('text' => '
    <p>Put an image next to the text. It can be on the left or right. Just select a different layout. It\'s easy as that.</p>
    <p>Add any widget to work with different types of content. There are many of them: title, text, separator, text with image, image, image gallery, logo gallery, file, table, HTML code, F.A.Q., Contact form.</p>
')); ?>

<?php echo ipRenderWidget('IpTitle', array('title' => 'Have a great experience to get most out of your website!'), 'level3'); ?>
