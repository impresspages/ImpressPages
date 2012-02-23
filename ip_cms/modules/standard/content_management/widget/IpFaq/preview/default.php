<?php
// preventing more than one function defined
if(!defined("IpFaqJS"))
{
    define("IpFaqJS", true);
    ?>
    <script type="text/javascript">
        //<![CDATA[
        function ipWidgetFaqShow(id){
            element = document.getElementById("ipWidgetFaqAnswer-" + id);
            if (element.style.display != "block")
                element.style.display = "block";
            else
                element.style.display = "none";
        }
        //]]>
    </script>
    <?php
}
?>
<a href="#" onclick="ipWidgetFaqShow(<? echo $this->data['instanceId']; ?>); return false;" class="ipWidgetFaqQuestion"><?php echo isset($question) ? htmlspecialchars($question) : ''; ?></a>
<div id="ipWidgetFaqAnswer-<? echo $this->data['instanceId']; ?>" class="ipWidgetFaqAnswer">
    <?php echo isset($answer) ? $answer : ''; ?>
</div>
