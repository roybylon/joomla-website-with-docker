<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<!-- Currency Selector Module -->
<?php echo $text_before ?>
<form id="select-form" class="xxx" action="<?php echo JURI::getInstance()->toString(); ?>" method="post">
	<?php echo JHTML::_('select.genericlist', $currencies, 'virtuemart_currency_id', 'class="inputbox"', 'virtuemart_currency_id', 'currency_txt', $virtuemart_currency_id) ; ?>
    <input class="button" type="submit" name="submit" value="<?php echo JText::_('Change') ?>" />
</form>
<?php 
$app			= JFactory::getApplication();
$doc			= JFactory::getDocument();
$templateparams	= $app->getTemplate(true)->params;
$template = $app->getTemplate();
$base = $doc->baseurl;
$path2 = $base.'/templates/'.$template;
?>
<script type="text/javascript" src="<?php echo $path2 ?>/html/mod_virtuemart_currencies/jqtransform.js"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery(function(){
		 jQuery('#select-form').jqTransform({imgPath:'<?php echo $path2 ?>/images/'}).css('display','block');
	});
		
});
</script>
