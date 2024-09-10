<?php
/**
*
* Layout for the shopping cart
*
* @package	VirtueMart
* @subpackage Cart
* @author Max Milbers
*
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: cart.php 2551 2010-09-30 18:52:40Z milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
vmJsApi::js ('facebox');
vmJsApi::css ('facebox');
JHtml::_ ('behavior.formvalidation');
$document = JFactory::getDocument ();
$document->addScriptDeclaration ("

//<![CDATA[
	jQuery(document).ready(function($) {
		$('div#full-tos').hide();
		$('a#terms-of-service').click(function(event) {
			event.preventDefault();
			$.facebox( { div: '#full-tos' }, 'my-groovy-style');
		});
	});

//]]>
");
$document->addScriptDeclaration ("

//<![CDATA[
	jQuery(document).ready(function($) {
	if ( $('#STsameAsBTjs').is(':checked') ) {
				$('#output-shipto-display').hide();
			} else {
				$('#output-shipto-display').show();
			}
		$('#STsameAsBTjs').click(function(event) {
			if($(this).is(':checked')){
				$('#STsameAsBT').val('1') ;
				$('#output-shipto-display').hide();
			} else {
				$('#STsameAsBT').val('0') ;
				$('#output-shipto-display').show();
			}
		});
	});

//]]>

");
$document->addStyleDeclaration ('#facebox .content {display: block !important; height: auto !important; overflow: auto; width: 560px !important; }');

//  vmdebug('car7t pricesUnformatted',$this->cart->pricesUnformatted);
//  vmdebug('cart pricesUnformatted',$this->cart->cartData );
?>

<?php if (VmConfig::get('oncheckout_show_steps', 1) && $this->checkout_task==='confirm'){
		vmdebug('checkout_task',$this->checkout_task);
		echo '<h1 class="checkoutStep" id="checkoutStep4">'.JText::_('COM_VIRTUEMART_USER_FORM_CART_STEP4').'</h1>';
	} ?>
<div class="cart-view">
		<h3><span><span><?php echo JText::_('TM_VIRTUEMART_CART_TITLE'); ?></span></span></h3>
	<div class="login-box">
	<div class="right-link">
		<?php // Continue Shopping Button
		if ($this->continue_link_html != '') {
			echo $this->continue_link_html;
		} ?>
	</div>
	<?php echo shopFunctionsF::getLoginForm($this->cart,false);
	//echo $this->loadTemplate('login');
	?>
	</div>
</div>

<div class="cart-view">
		<h3><span><span><?php echo JText::_('TM_VIRTUEMART_CART_BILLING'); ?></span></span></h3>
		<div class="billing-box">
			<div class="billto-shipto">
	<div class="width50 floatleft">

		<h1><span class="vmicon vm2-billto-icon"></span>
		<?php echo JText::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'); ?></h1>
		<?php // Output Bill To Address ?>
		<div class="output-billto">
		<?php

		foreach($this->cart->BTaddress['fields'] as $item){
			if(!empty($item['value'])){
				if($item['name']==='agreed'){
					$item['value'] =  ($item['value']===0) ? JText::_('COM_VIRTUEMART_USER_FORM_BILLTO_TOS_NO'):JText::_('COM_VIRTUEMART_USER_FORM_BILLTO_TOS_YES');
				}
				?><!-- span class="titles"><?php echo $item['title'] ?></span -->
					<span class="values vm2<?php echo '-'.$item['name'] ?>" ><?php echo $this->escape($item['value']) ?></span>
				<?php if ($item['name'] != 'title' and $item['name'] != 'first_name' and $item['name'] != 'middle_name' and $item['name'] != 'zip') { ?>
					<br class="clear" />
				<?php
				}
			}
		} ?>
		<div class="clear"></div>
		</div>

		<a class="details" href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=user&task=editaddresscart&addrtype=BT',$this->useXHTML,$this->useSSL) ?>">
		<?php echo JText::_('COM_VIRTUEMART_USER_FORM_EDIT_BILLTO_LBL'); ?>
		</a>

		<input type="hidden" name="billto" value="<?php echo $this->cart->lists['billTo']; ?>"/>
	</div>

	<div class="width50 floatleft">

		<h1><span class="vmicon vm2-shipto-icon"></span>
		<?php echo JText::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); ?></h1>
		<?php // Output Bill To Address ?>
		<div class="output-shipto">
		<?php
		if(empty($this->cart->STaddress['fields'])){
			echo JText::sprintf('TM_VIRTUEMART_USER_FORM_EDIT_BILLTO_EXPLAIN',JText::_('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL') );
		} else {
			if(!class_exists('VmHtml'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'html.php');
			echo JText::_('COM_VIRTUEMART_USER_FORM_ST_SAME_AS_BT'). VmHtml::checkbox('STsameAsBT',$this->cart->STsameAsBT).'<br />';
			foreach($this->cart->STaddress['fields'] as $item){
				if(!empty($item['value'])){ ?>
					<!-- <span class="titles"><?php echo $item['title'] ?></span> -->
					<?php
					if ($item['name'] == 'first_name' || $item['name'] == 'middle_name' || $item['name'] == 'zip') { ?>
						<span class="values<?php echo '-'.$item['name'] ?>" ><?php echo $this->escape($item['value']) ?></span>
					<?php } else { ?>
						<span class="values" ><?php echo $this->escape($item['value']) ?></span>
						<br class="clear" />
					<?php
					}
				}
			}
		}
 		?>
		<div class="clear"></div>
		</div>
		<?php if(!isset($this->cart->lists['current_id'])) $this->cart->lists['current_id'] = 0; ?>
		<a class="details" href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=user&task=editaddresscart&addrtype=ST&cid[]='.$this->cart->lists['current_id'],$this->useXHTML,$this->useSSL) ?>">
		<?php echo JText::_('COM_VIRTUEMART_USER_FORM_ADD_SHIPTO_LBL'); ?>
		</a>

	</div>

	<div class="clear"></div>
</div>
		</div>
</div>

<div class="cart-view" style="margin-bottom:0;">
	<h3><span><span><?php echo JText::_('TM_VIRTUEMART_CART_ORDER'); ?></span></span></h3>
	<div class="billing-box">
	<?php
	if (($this->cart->pricesUnformatted['salesPrice'])>0) {  ?>
		<fieldset>
	<table
		class="cart-summary"
		cellspacing="0"
		cellpadding="0"
		border="0"
		width="100%">
		<tr>
			<th align="left"><?php echo JText::_('COM_VIRTUEMART_CART_NAME') ?></th>
			<th align="left"><?php echo JText::_('COM_VIRTUEMART_CART_SKU') ?></th>
			<th
				align="center"
				width="60px"><?php echo JText::_('COM_VIRTUEMART_CART_PRICE') ?></th>
			<th
				align="right"
				width="120px"><?php echo JText::_('COM_VIRTUEMART_CART_QUANTITY') ?>
				/ <?php echo JText::_('COM_VIRTUEMART_CART_ACTION') ?></th>


                                        <?php if ( VmConfig::get('show_tax')) { ?>
                                <th align="right" width="60px"><?php  echo "<span  class='priceColor2'>".JText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT') ?></th>
				<?php } ?>
                                <th align="right" width="60px"><?php echo "<span  class='priceColor2'>".JText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT') ?></th>
				<th align="right" width="70px"><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL') ?></th>
			</tr>



		<?php
		$i=1;
// 		vmdebug('$this->cart->products',$this->cart->products);
		foreach( $this->cart->products as $pkey =>$prow ) { ?>
			<tr valign="top" class="sectiontableentry<?php echo $i ?>">
				<td align="center" >
					<?php if ( $prow->virtuemart_media_id) {  ?>
						<span class="cart-images">
						 <?php
						 if(!empty($prow->image)) echo '<a href="'.$prow->url.'" >'.$prow->image->displayMediaThumb('',false).'</a>';
						 ?>
						</span>
					<?php } ?>
					<span class="cart-title"><?php echo JHTML::link($prow->url, $prow->product_name); ?></span>
					
					<?php
					if ( $prow->customfields) 
					{
					echo $prow->customfields; 
					}
					?>
					

				</td>
				<td align="center" ><?php  echo $prow->product_sku ?></td>
				<td align="center" >
				<?php
// 					vmdebug('$this->cart->pricesUnformatted[$pkey]',$this->cart->pricesUnformatted[$pkey]['priceBeforeTax']);
					echo $this->currencyDisplay->createPriceDiv('basePriceVariant','', $this->cart->pricesUnformatted[$pkey],false);
// 					echo $prow->salesPrice ;
					?>
				</td>
				<td align="left" >
				<?php 
//				$step=$prow->min_order_level; 
				if ($prow->step_order_level)
					$step=$prow->step_order_level;
				else
					$step=1;
				if($step==0)
					$step=1;
				$alert=JText::sprintf ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED', $step);
				?> 
                <script type="text/javascript">
				function check<?php echo $step?>(obj) {
 				// use the modulus operator '%' to see if there is a remainder
				remainder=obj.value % <?php echo $step?>;
				quantity=obj.value;
 				if (remainder  != 0) {
 					alert('<?php echo $alert?>!');
 					obj.value = quantity-remainder;
 					return false;
 				}
 				return true;
 				}
				</script> 
				<form action="<?php JRoute::_('index.php'); ?>" method="post" class="inline">
				<input type="hidden" name="option" value="com_virtuemart" />
				<input type="text" onblur="check<?php echo $step?>(this);" onclick="check<?php echo $step?>(this);" onchange="check<?php echo $step?>(this);" onsubmit="check(<?php echo $step?>this);" title="<?php echo  JText::_('COM_VIRTUEMART_CART_UPDATE') ?>" class="quantity-input js-recalculate" size="3" maxlength="4" name="quantity" value="<?php echo $prow->quantity ?>" />
			<input type="hidden" name="view" value="cart"/>
			<input type="hidden" name="task" value="update"/>
			<input type="hidden" name="cart_virtuemart_product_id" value="<?php echo $prow->cart_item_id  ?>"/>
			<input type="submit" class="vmicon vm2-add_quantity_cart" name="update" title="<?php echo  JText::_ ('COM_VIRTUEMART_CART_UPDATE') ?>" align="middle" value=" "/>
			  </form>
					<a class="vmicon vm2-remove_from_cart" title="<?php echo JText::_('COM_VIRTUEMART_CART_DELETE') ?>" align="middle" href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=cart&task=delete&cart_virtuemart_product_id='.$prow->cart_item_id  ) ?>"> </a>
				</td>

				<?php if ( VmConfig::get('show_tax')) { ?>
				<td align="center"><?php if ( !empty($this->cart->pricesUnformatted[$pkey]['taxAmount']) ) 
				{
					echo "<span class='priceColor2'>".$this->currencyDisplay->createPriceDiv('taxAmount','', $this->cart->pricesUnformatted[$pkey],false,false,$prow->quantity)."</span>";
				} else 
				{
					echo "--";
				}
			?> 
			
			</td>
                                <?php } ?>
				<td align="center"><?php if ( !empty($this->cart->pricesUnformatted[$pkey]['discountAmount']) )
				{
				echo "<span class='priceColor2'>".$this->currencyDisplay->createPriceDiv('discountAmount','', $this->cart->pricesUnformatted[$pkey],false,false,$prow->quantity)."</span>";
				} else 
				{
					echo "--";
				}
				?></td>
				<td align="center">
				<?php
				if (VmConfig::get('checkout_show_origprice',1) && !empty($this->cart->pricesUnformatted[$pkey]['basePriceWithTax']) && $this->cart->pricesUnformatted[$pkey]['basePriceWithTax'] != $this->cart->pricesUnformatted[$pkey]['salesPrice'] ) {
					echo '<span class="line-through">'.$this->currencyDisplay->createPriceDiv('basePriceWithTax','', $this->cart->pricesUnformatted[$pkey],true,false,$prow->quantity) .'</span>' ;
				}
				echo $this->currencyDisplay->createPriceDiv('salesPrice','', $this->cart->pricesUnformatted[$pkey],false,false,$prow->quantity) ?></td>
			</tr>
		<?php
			$i = 1 ? 2 : 1;
		} ?>
		<!--Begin of SubTotal, Tax, Shipment, Coupon Discount and Total listing -->
                  <?php if ( VmConfig::get('show_tax')) { $colspan=3; } else { $colspan=2; } ?>
		<tr class="pad">
		 <td></td>
		</tr>
		  <tr class="sectiontableentry1 bg-top">
			<td colspan="4" align="right"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'); ?>:</td>

                        <?php if ( VmConfig::get('show_tax')) { ?>
			<td align="center"><?php if ( !empty($this->cart->pricesUnformatted[$pkey]['taxAmount']) )
			{
			echo "<span  class='priceColor2'>".$this->currencyDisplay->createPriceDiv('taxAmount','', $this->cart->pricesUnformatted,false)."</span>"; 
			} else 
				{
					echo "--";
				}
			?></td>
                        <?php } ?>
			<td align="center"><?php  if ( !empty($this->cart->pricesUnformatted['billDiscountAmount']) ) 
			{
				echo "<span  class='priceColor2'>".$this->currencyDisplay->createPriceDiv('billDiscountAmount','', $this->cart->pricesUnformatted['billDiscountAmount'],false)."</span>";
			} else 
				{
					echo "--";
				}
			?></td>
			<td align="center"><?php echo $this->currencyDisplay->createPriceDiv('salesPrice','', $this->cart->pricesUnformatted,false) ?></td>
		  </tr>
		<?php
		foreach($this->cart->cartData['DBTaxRulesBill'] as $rule){ ?>
			<tr class="sectiontableentry<?php $i ?>">
				<td colspan="4" align="right"><?php echo $rule['calc_name'] ?> </td>

                                   <?php if ( VmConfig::get('show_tax')) { ?>
				<td align="right"> </td>
                                <?php } ?>
				<td align="right"><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],false); ?></td>
				<td align="right"><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],false); ?> </td>
			</tr>
			<?php
			if($i) $i=1; else $i=0;
		} ?>

		<?php

		foreach($this->cart->cartData['taxRulesBill'] as $rule){ ?>
			<tr class="sectiontableentry<?php $i ?>">
				<td colspan="4" align="right"><?php echo $rule['calc_name'] ?> </td>
				<?php if ( VmConfig::get('show_tax')) { ?>
				<td align="right"><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],false); ?> </td>
				 <?php } ?>
				<td align="right"><?php ?> </td>
				<td align="right"><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],false); ?> </td>
			</tr>
			<?php
			if($i) $i=1; else $i=0;
		}

		foreach($this->cart->cartData['DATaxRulesBill'] as $rule){ ?>
			<tr class="sectiontableentry<?php $i ?>">
				<td colspan="4" align="right"><?php echo   $rule['calc_name'] ?> </td>

                                     <?php if ( VmConfig::get('show_tax')) { ?>
				<td align="right"> </td>

                                <?php } ?>
				<td align="right"><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],false); ?>  </td>
				<td align="right"><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'].'Diff','', $this->cart->pricesUnformatted[$rule['virtuemart_calc_id'].'Diff'],false); ?> </td>
			</tr>
			<?php
			if($i) $i=1; else $i=0;
		} ?>

	<tr class="pad"><td></td></tr>
                    <tr class="sectiontableentry1 bg-top">
	<?php if (!$this->cart->automaticSelectedShipment) { ?>

	<?php /*	<td colspan="2" align="right"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIPPING'); ?> </td> */ ?>
				<td colspan="4" align="center">
				<?php echo $this->cart->cartData['shipmentName']; ?>
	<br/>
	<?php
	if (!empty($this->layoutName) && $this->layoutName == 'default' && !$this->cart->automaticSelectedShipment) {
		echo JHTML::_ ('link', JRoute::_ ('index.php?view=cart&task=edit_shipment', $this->useXHTML, $this->useSSL), $this->select_shipment_text, 'class=""');
	} else {
		JText::_ ('COM_VIRTUEMART_CART_SHIPPING');
	}
} else {
	?>
	<td colspan="4" align="center">
		<?php echo $this->cart->cartData['shipmentName'].'<br/>';
		?>
	</td>
	<?php } ?>

	<?php if (VmConfig::get ('show_tax')) { ?>
	<td align="center"><?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('shipmentTax', '', $this->cart->pricesUnformatted['shipmentTax'], FALSE) . "</span>"; ?> </td>
	<?php } ?>
	<td align="center">--</td>
	<td align="center"><?php echo $this->currencyDisplay->createPriceDiv ('salesPriceShipment', '', $this->cart->pricesUnformatted['salesPriceShipment'], FALSE); ?> </td>
</tr>

		<?php if ($this->cart->pricesUnformatted['salesPrice']>0.0 ) { ?>
<tr class="sectiontableentry1">
	<?php if (!$this->cart->automaticSelectedPayment) { ?>

	<td colspan="4" align="center">
		<?php echo $this->cart->cartData['paymentName']; ?>
		<br/>
		<?php if (!empty($this->layoutName) && $this->layoutName == 'default') {
		echo JHTML::_ ('link', JRoute::_ ('index.php?view=cart&task=editpayment', $this->useXHTML, $this->useSSL), $this->select_payment_text, 'class=""');
	} else {
		JText::_ ('COM_VIRTUEMART_CART_PAYMENT');
	} ?> </td>

	</td>
	<?php } else { ?>
	<td colspan="4" align="center"><?php echo $this->cart->cartData['paymentName']; ?> </td>
	<?php } ?>
	<?php if (VmConfig::get ('show_tax')) { ?>
	<td align="center"><?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv ('paymentTax', '', $this->cart->pricesUnformatted['paymentTax'], FALSE) . "</span>"; ?> </td>
	<?php } ?>
	<td align="center">--<?php // Why is this commented? what is with payment discounts? echo "<span  class='priceColor2'>".$this->cart->pricesUnformatted['paymentDiscount']."</span>"; ?></td>
	<td align="center"><?php  echo $this->currencyDisplay->createPriceDiv ('salesPricePayment', '', $this->cart->pricesUnformatted['salesPricePayment'], FALSE); ?> </td>
</tr>
<?php } ?>
			<?php
				if (VmConfig::get ('coupons_enable')) {
					?>
				<tr class="sectiontableentry2 coupon-tr">
				<td colspan="4" align="left">
					<?php if (!empty($this->layoutName) && $this->layoutName == 'default') {
					// echo JHTML::_('link', JRoute::_('index.php?view=cart&task=edit_coupon',$this->useXHTML,$this->useSSL), JText::_('COM_VIRTUEMART_CART_EDIT_COUPON'));
					echo $this->loadTemplate ('coupon');
				}
					?>
					<?php if (!empty($this->cart->cartData['couponCode'])) { ?>
					<?php
					echo $this->cart->cartData['couponCode'];
					echo $this->cart->cartData['couponDescr'] ? (' (' . $this->cart->cartData['couponDescr'] . ')') : '';
					?>
				
								</td>
				
					 <?php if (VmConfig::get ('show_tax')) { ?>
						<td align="right"><?php echo $this->currencyDisplay->createPriceDiv ('couponTax', '', $this->cart->pricesUnformatted['couponTax'], FALSE); ?> </td>
					<?php } ?>
					<td align="center">--</td>
					<td align="center"><?php echo $this->currencyDisplay->createPriceDiv ('salesPriceCoupon', '', $this->cart->pricesUnformatted['salesPriceCoupon'], FALSE); ?> </td>
					<?php } else { ?>
					<td colspan="6" align="left">&nbsp;</td>
					<?php
				}
				
					?>
</tr>
	<?php } ?>

			
		   <tr class="pad">
		 <td></td>
		</tr>
		  <tr class="sectiontableentry2 bg-top">
			<td colspan="4" align="right"><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL') ?>: </td>

                        <?php if ( VmConfig::get('show_tax')) { ?>
			<td align="right"> <?php echo "<span  class='priceColor2'>".$this->currencyDisplay->createPriceDiv('billTaxAmount','', $this->cart->pricesUnformatted['billTaxAmount'],false)."</span>" ?> </td>
                        <?php } ?>
			<td align="center"> <?php echo "<span  class='priceColor2'>".$this->currencyDisplay->createPriceDiv('billDiscountAmount','', $this->cart->pricesUnformatted['billDiscountAmount'],false)."</span>" ?> </td>
			<td align="center" class="color"><strong><?php echo $this->currencyDisplay->createPriceDiv('billTotal','', $this->cart->pricesUnformatted['billTotal'],false); ?></strong></td>
		  </tr>
		    <?php
		    if ( $this->totalInPaymentCurrency) {
			?>

		       <?php /*?><tr class="sectiontableentry2 bg-top">
					    <td colspan="4" align="right"><?php echo JText::_('COM_VIRTUEMART_CART_TOTAL_PAYMENT') ?>: </td>

					    <?php if ( VmConfig::get('show_tax')) { ?>
					    <td align="right">  </td>
					    <?php } ?>
					    <td align="right">  </td>
					    <td align="right"><strong><?php echo $this->currencyDisplay->createPriceDiv('totalInPaymentCurrency','', $this->totalInPaymentCurrency,false); ?></strong></td>
				      </tr><?php */?>
				      <?php
		    }
		    ?>


	</table>
</fieldset>
<?php } else {echo '<h1>'.JText::_('TM_VIRTUEMART_ADD_PRODUCT_TO').'</h1>';} 

if ($this->checkout_task) {
		$taskRoute = '&task=' . $this->checkout_task;
	}
	else {
		$taskRoute = '';
	}

?>
<div id="checkout-advertise-box">
		<?php
		if (!empty($this->checkoutAdvertise)) {
			foreach ($this->checkoutAdvertise as $checkoutAdvertise) {
				?>
				<div class="checkout-advertise">
					<?php echo $checkoutAdvertise; ?>
				</div>
				<?php
			}
		}
		?>
	</div>
<form method="post" id="checkoutForm" name="checkoutForm" action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=cart' . $taskRoute, $this->useXHTML, $this->useSSL); ?>">

		<?php // Leave A Comment Field ?>
		<div class="customer-comment marginbottom15">
			<span class="comment"><?php echo JText::_ ('COM_VIRTUEMART_COMMENT_CART'); ?></span><br/>
			<textarea class="customer-comment" name="customer_comment" cols="60" rows="1"><?php echo $this->cart->customer_comment; ?></textarea>
		</div>
		<?php // Leave A Comment Field END ?>



		<?php // Continue and Checkout Button ?>
		<div class="checkout-button-top">

			<?php // Terms Of Service Checkbox
			if (!class_exists ('VirtueMartModelUserfields')) {
				require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'userfields.php');
			}
			$userFieldsModel = VmModel::getModel ('userfields');
			if ($userFieldsModel->getIfRequired ('agreed')) {
				?>
				<label for="tosAccepted" id="tosAcceptedLabel">
					<?php
					if (!class_exists ('VmHtml')) {
						require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'html.php');
					}
					echo VmHtml::checkbox ('tosAccepted', $this->cart->tosAccepted, 1, 0, 'class="terms-of-service"');

					if (VmConfig::get ('oncheckout_show_legal_info', 1)) {
						?>
						<div class="terms-of-service">

							<a href="<?php JRoute::_ ('index.php?option=com_virtuemart&view=vendor&layout=tos&virtuemart_vendor_id=1') ?>" class="terms-of-service" id="terms-of-service" rel="facebox"
							   target="_blank">
								<span class="vmicon vm2-termsofservice-icon"></span>
								<?php echo JText::_ ('COM_VIRTUEMART_CART_TOS_READ_AND_ACCEPTED'); ?>
							</a>

							<div id="full-tos">
								<h2><?php echo JText::_ ('COM_VIRTUEMART_CART_TOS'); ?></h2>
								<?php echo $this->cart->vendor->vendor_terms_of_service; ?>
							</div>

						</div>
						<?php
					} // VmConfig::get('oncheckout_show_legal_info',1)
					//echo '<span class="tos">'. JText::_('COM_VIRTUEMART_CART_TOS_READ_AND_ACCEPTED').'</span>';
					?>
				</label>
				<?php
			}
			echo $this->checkout_link_html;
			?>
		</div>
		<?php // Continue and Checkout Button END ?>
		<input type='hidden' id='STsameAsBT' name='STsameAsBT' value='<?php echo $this->cart->STsameAsBT; ?>'/>
		<input type='hidden' name='task' value='<?php echo $this->checkout_task; ?>'/>
		<input type='hidden' name='option' value='com_virtuemart'/>
		<input type='hidden' name='view' value='cart'/>
	</form>
		
	</div>
</div>
