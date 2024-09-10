<?php defined('_JEXEC') or die('Restricted access');
error_reporting('E_ALL');

// Separator
$verticalseparator = " vertical-separator"; ?>

<?php
foreach ($this->products as $type => $productList ) {
	if ($type != 'topten') continue;
// Calculating Products Per Row
$products_per_row = VmConfig::get ( 'homepage_products_per_row', 3 ) ;
//print_r($products_per_row);
$cellwidth = floor ( 100 / $products_per_row );
// Category and Columns Counter
$col = 1;
$nb = 1;

$productTitle = JText::_('COM_VIRTUEMART_BESTSELLERS');

$ratingModel = VmModel::getModel('ratings');

?>

<div class="<?php echo $type ?>-view">

	<h1><?php echo $productTitle ?></h1>
<div  class="browse-view front">

<?php // Start the Output
foreach ( $productList as $product ) {
	if (isset($product->step_order_level))
							$step=$product->step_order_level;
						else
							$step=1;
						if($step==0)
							$step=1;
						$alert=JText::sprintf ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED', $step);

	// Show the horizontal seperator
	if ($col == 1 && $nb > $products_per_row) { ?>
	<div class="horizontal-separator"></div>
	<?php }

	// this is an indicator wether a row needs to be opened or not
	if ($col == 1) { ?>
	<div class="row boxIndent">
	<?php }

	// Show the vertical seperator
	if ($nb == $products_per_row or $nb % $products_per_row == 0) {
		$show_vertical_separator = ' ';
	} else {
		$show_vertical_separator = $verticalseparator;
	}

		// Show Products ?>
		<div class="product floatleft<?php echo $show_vertical_separator ?>" style="width:<?php echo $cellwidth ?>%"
>
			<div class="spacer <?php if (abs($product->prices[discountAmount]) > 0) { echo 'disc'; } ?>">
					
				<div class="floatleft col-1">
					<div class="browseProductImageContainer">
					
					<?php /** @todo make image popup */
					$imgsmall = JURI::base(true).'/'.$product->images[0]->file_url;
echo JHTML::link($product->link, $product->images[0]->displayMediaThumb('class="browseProductImage" border="0" title="'.$product->product_name.'" ',false ), 'rel="'.$imgsmall.'" class="img-scr"');
						?>
					</div>
					
				</div>
					
					<div class="floatleft col-3">
										<div class="title-indent">
											<h2><?php echo JHTML::link($product->link, shopFunctionsF::limitStringByWord($product->product_name,50, '...')); ?></h2>
												<?php // Product Short Description
												if(!empty($product->product_s_desc)) { ?>
												<?php echo shopFunctionsF::limitStringByWord($product->product_s_desc, 100, '...') ?>
												<?php } ?>
												
											<div class="rating">
											
													<?php
																										$rating = $ratingModel->getRatingByProduct($product->virtuemart_product_id);
													
													if( !empty($rating)) {
													$r = $rating->rating;
													} else {
													$r = 0;
													}
													$maxrating = VmConfig::get('vm_maximum_rating_scale',5);
													$ratingwidth = ( $r * 100 ) / $maxrating;//I don't use round as percetntage with works perfect, as for me
													?>
													
													<span class="vote">
													<span title="" class="vmicon ratingbox" style="display:inline-block;">
													<span class="stars-orange" style="width:<?php echo $ratingwidth;?>%">
													</span>
													</span>
													</span>


											</div>	
										</div>
									</div>
					<div class="floatright col-2">
					<?php if (!VmConfig::get('use_as_catalog')) {?>
							<div class="product-price marginbottom12" id="productPrice<?php echo $product->virtuemart_product_id ?>">
								<?php
									echo $this->currency->createPriceDiv('salesPrice','',$product->prices);
									//print_r($product->prices);
									if (abs($product->prices[discountAmount]) > 0) {
										echo $this->currency->createPriceDiv('priceWithoutTax','',$product->prices);
									}
									//echo $this->currency->createPriceDiv('discountAmount','COM_VIRTUEMART_PRODUCT_DISCOUNT_AMOUNT',$product->prices);
									
									//echo $this->currency->createPriceDiv('salesPrice','COM_VIRTUEMART_PRODUCT_SALESPRICE',$product->prices);
									//echo $this->currency->createPriceDiv('priceWithoutTax','COM_VIRTUEMART_PRODUCT_SALESPRICE_WITHOUT_TAX',$product->prices);
									
									//echo $this->currency->createPriceDiv('variantModification','COM_VIRTUEMART_PRODUCT_VARIANT_MOD',$product->prices);
									//echo $this->currency->createPriceDiv('basePriceWithTax','COM_VIRTUEMART_PRODUCT_BASEPRICE_WITHTAX',$product->prices);
									//echo $this->currency->createPriceDiv('discountedPriceWithoutTax','COM_VIRTUEMART_PRODUCT_DISCOUNTED_PRICE',$product->prices);
									//echo $this->currency->createPriceDiv('salesPriceWithDiscount','COM_VIRTUEMART_PRODUCT_SALESPRICE_WITH_DISCOUNT',$product->prices);
									//echo $this->currency->createPriceDiv('taxAmount','COM_VIRTUEMART_PRODUCT_TAX_AMOUNT',$product->prices);
								 ?>
							</div>
						<?php }?>	
						<?php if (!VmConfig::get('use_as_catalog')) {?>
						<div class="addtocart-area">
							<form method="post" class="product js-recalculate" action="index.php" id="addtocartproduct<?php echo $product->virtuemart_product_id ?>">
							 <input name="quantity" type="hidden" value="<?php echo $step ?>" />
										<div class="addtocart-bar2">
												<script type="text/javascript">
		function check(obj) {
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
											<?php // Display the quantity box 

											if (($product->product_in_stock < 1) || (($product->product_in_stock - $product->product_ordered) < $product->min_order_level  ) ) {
										 ?>
										<span class="addtocart-button">
											<i>&gt;</i>
												<a class="addtocart-button" href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id='.$product->virtuemart_product_id); ?>"><?php echo JText::_('COM_VIRTUEMART_CART_NOTIFY') ?></a>
												</span>
										<?php } else { ?>	
										<div class="not_notyfi">
										<?php // Display the quantity box 
											if ((!empty($product->customsChilds)) || (!empty($product->customfieldsCart))) { ?>
											<span class="attributes"><b>*</b> Product has attributes</span>
											<span class="addtocart-button">
											<i>&gt;</i>
										<?php echo JHTML::link($product->link, JText::_('COM_VIRTUEMART_CART_ADD_TO'), array('title' => $product->product_name,'class' => 'addtocart-button atribut')); ?>
										</span>
										<?php } else { ?>
										<?php if ($products_per_row <5){ ?>
											<label for="quantity<?php echo $this->product->virtuemart_product_id;?>" class="quantity_box"><?php echo JText::_('COM_VIRTUEMART_CART_QUANTITY'); ?>: </label>
										<?php } ?>	
											<span class="quantity-box">
											<input type="text" class="quantity-input js-recalculate" name="quantity[]" onblur="check(this);" value="<?php if (isset($product->step_order_level) && (int)$product->step_order_level > 0) {
			echo $product->step_order_level;
		} else if(!empty($product->min_order_level)){
			echo $product->min_order_level;
		}else {
			echo '1';
		} ?>"/>
											</span>
											<span class="quantity-controls">
											<input type="button" class="quantity-controls quantity-plus" />
											<input type="button" class="quantity-controls quantity-minus" />
											</span>
											<?php // Add the button
											$button_lbl = JText::_('COM_VIRTUEMART_CART_ADD_TO');
											$button_cls = 'addtocart-button cart-click'; //$button_cls = 'addtocart_button';
											?>
											<?php // Display the add to cart button ?>
											<div class="clear"></div>
											<span class="addtocart-button">
											<i>&gt;</i>
												<input type="submit" name="<?php echo $button_name ?>"  class="<?php echo $button_cls ?>" value="<?php echo $button_lbl ?>" title="<?php echo $button_lbl ?>" />
											</span>
											<?php if ($products_per_row <=3){ ?>
											<div class="det">
												<?php // Product Details Button
												echo JHTML::link($product->link, JText::_('TM_DETAILS').'<span>&gt;</span>', array('title' => $product->product_name,'class' => 'button'));
												?>
											</div>
											<?php } ?>
											<div class="clear"></div>
											<?php }?>
											</div>
											<?php }?>
										
										</div>
										<input type="hidden" class="pname" value="<?php echo $product->product_name ?>"/>
										<input type="hidden" name="option" value="com_virtuemart" />
										<input type="hidden" name="view" value="cart" />
										<noscript><input type="hidden" name="task" value="add" /></noscript>
										<input type="hidden" name="virtuemart_product_id[]" value="<?php echo $product->virtuemart_product_id ?>" />
										<input type="hidden" name="virtuemart_category_id[]" value="<?php echo $product->virtuemart_category_id ?>" />
										
									</form>
						</div>
					<?php }?>
						</div>
					
					

					
			</div>
		</div>
	<?php
	$nb ++;

	// Do we need to close the current row now?
	if ($col == $products_per_row) { ?>
	<div class="clear"></div>
	</div>
		<?php
		$col = 1;
	} else {
		$col ++;
	}
}
// Do we need a final closing row tag?
if ($col != 1) { ?>
	<div class="clear"></div>
	</div>
<?php
}
?>
</div>
</div>
<?php } ?>

<?php
foreach ($this->products as $type => $productList ) {
	if ($type == 'topten') continue;
// Calculating Products Per Row
$products_per_row = VmConfig::get ( 'homepage_products_per_row', 3 ) ;
$cellwidth = floor ( 100 / $products_per_row );

// Category and Columns Counter
$col = 1;
$nb = 1;

$productTitle = JText::_('COM_VIRTUEMART_'.$type.'_PRODUCT');

$ratingModel = VmModel::getModel('ratings');

?>

<div class="<?php echo $type ?>-view">

	<h1><?php echo $productTitle ?></h1>
<div  class="browse-view front">

<?php // Start the Output
foreach ( $productList as $product ) {
		if (isset($product->step_order_level))
							$step=$product->step_order_level;
						else
							$step=1;
						if($step==0)
							$step=1;
						$alert=JText::sprintf ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED', $step);


	// Show the horizontal seperator
	if ($col == 1 && $nb > $products_per_row) { ?>
	<div class="horizontal-separator"></div>
	<?php }

	// this is an indicator wether a row needs to be opened or not
	if ($col == 1) { ?>
	<div class="row boxIndent">
	<?php }

	// Show the vertical seperator
	if ($nb == $products_per_row or $nb % $products_per_row == 0) {
		$show_vertical_separator = ' ';
	} else {
		$show_vertical_separator = $verticalseparator;
	}

		// Show Products ?>
		<div class="product floatleft<?php echo $show_vertical_separator ?>" style="width:<?php echo $cellwidth ?>%"
>
			<div class="spacer <?php if (abs($product->prices[discountAmount]) > 0) { echo 'disc'; } ?>">
					
				<div class="floatleft col-1">
					<div class="browseProductImageContainer">
					
					<?php /** @todo make image popup */
					$imgsmall = JURI::base(true).'/'.$product->images[0]->file_url;
echo JHTML::link($product->link, $product->images[0]->displayMediaThumb('class="browseProductImage" border="0" title="'.$product->product_name.'" ',false ), 'rel="'.$imgsmall.'" class="img-scr"');
						?>
					</div>
					
				</div>
					
					<div class="floatleft col-3">
										<div class="title-indent">
											<h2><?php echo JHTML::link($product->link, shopFunctionsF::limitStringByWord($product->product_name,50, '...')); ?></h2>
												<?php // Product Short Description
												if(!empty($product->product_s_desc)) { ?>
												<?php echo shopFunctionsF::limitStringByWord($product->product_s_desc, 100, '...') ?>
												<?php } ?>
												
											<div class="rating">
											
													<?php
																										$rating = $ratingModel->getRatingByProduct($product->virtuemart_product_id);
													
													if( !empty($rating)) {
													$r = $rating->rating;
													} else {
													$r = 0;
													}
													$maxrating = VmConfig::get('vm_maximum_rating_scale',5);
													$ratingwidth = ( $r * 100 ) / $maxrating;//I don't use round as percetntage with works perfect, as for me
													?>
													
													<span class="vote">
													<span title="" class="vmicon ratingbox" style="display:inline-block;">
													<span class="stars-orange" style="width:<?php echo $ratingwidth;?>%">
													</span>
													</span>
													</span>


											</div>	
										</div>
									</div>
					<div class="floatright col-2">
					<?php if (!VmConfig::get('use_as_catalog')) {?>
							<div class="product-price marginbottom12" id="productPrice<?php echo $product->virtuemart_product_id ?>">
								<?php
									echo $this->currency->createPriceDiv('salesPrice','',$product->prices);
									//print_r($product->prices);
									if (abs($product->prices[discountAmount]) > 0) {
										echo $this->currency->createPriceDiv('priceWithoutTax','',$product->prices);
									}
									//echo $this->currency->createPriceDiv('discountAmount','COM_VIRTUEMART_PRODUCT_DISCOUNT_AMOUNT',$product->prices);
									
									//echo $this->currency->createPriceDiv('salesPrice','COM_VIRTUEMART_PRODUCT_SALESPRICE',$product->prices);
									//echo $this->currency->createPriceDiv('priceWithoutTax','COM_VIRTUEMART_PRODUCT_SALESPRICE_WITHOUT_TAX',$product->prices);
									
									//echo $this->currency->createPriceDiv('variantModification','COM_VIRTUEMART_PRODUCT_VARIANT_MOD',$product->prices);
									//echo $this->currency->createPriceDiv('basePriceWithTax','COM_VIRTUEMART_PRODUCT_BASEPRICE_WITHTAX',$product->prices);
									//echo $this->currency->createPriceDiv('discountedPriceWithoutTax','COM_VIRTUEMART_PRODUCT_DISCOUNTED_PRICE',$product->prices);
									//echo $this->currency->createPriceDiv('salesPriceWithDiscount','COM_VIRTUEMART_PRODUCT_SALESPRICE_WITH_DISCOUNT',$product->prices);
									//echo $this->currency->createPriceDiv('taxAmount','COM_VIRTUEMART_PRODUCT_TAX_AMOUNT',$product->prices);
								 ?>
							</div>
						<?php }?>	
						<?php if (!VmConfig::get('use_as_catalog')) {?>
						<div class="addtocart-area">
							<form method="post" class="product js-recalculate" action="index.php" id="addtocartproduct<?php echo $product->virtuemart_product_id ?>">
							 <input name="quantity" type="hidden" value="<?php echo $step ?>" />
							<script type="text/javascript">
		function check(obj) {
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
										<div class="addtocart-bar2">
												
											<?php // Display the quantity box 

											if (($product->product_in_stock < 1) || (($product->product_in_stock - $product->product_ordered) < $product->min_order_level  ) ) {
										 ?>
										 <span class="addtocart-button">
											<i>&gt;</i>
												<a class="addtocart-button" href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id='.$product->virtuemart_product_id); ?>"><?php echo JText::_('COM_VIRTUEMART_CART_NOTIFY') ?></a>
												</span>
												
										<?php } else { ?>	
										<div class="not_notyfi">
										<?php // Display the quantity box 
											if ((!empty($product->customsChilds)) || (!empty($product->customfieldsCart))) { ?>
											<span class="attributes"><b>*</b> Product has attributes</span>
											<span class="addtocart-button">
											<i>&gt;</i>
										<?php echo JHTML::link($product->link, JText::_('COM_VIRTUEMART_CART_ADD_TO'), array('title' => $product->product_name,'class' => 'addtocart-button atribut')); ?>
										</span>
										<?php } else { ?>
										<?php if ($products_per_row <5){ ?>
											<label for="quantity<?php echo $this->product->virtuemart_product_id;?>" class="quantity_box"><?php echo JText::_('COM_VIRTUEMART_CART_QUANTITY'); ?>: </label>
										<?php } ?>	
											<span class="quantity-box">
											<input type="text" class="quantity-input js-recalculate" name="quantity[]" onblur="check(this);" value="<?php if (isset($product->step_order_level) && (int)$product->step_order_level > 0) {
			echo $product->step_order_level;
		} else if(!empty($product->min_order_level)){
			echo $product->min_order_level;
		}else {
			echo '1';
		} ?>"/>
											</span>
											<span class="quantity-controls">
											<input type="button" class="quantity-controls quantity-plus" />
											<input type="button" class="quantity-controls quantity-minus" />
											</span>
											<?php // Add the button
											$button_lbl = JText::_('COM_VIRTUEMART_CART_ADD_TO');
											$button_cls = 'addtocart-button cart-click'; //$button_cls = 'addtocart_button';
											?>
											<?php // Display the add to cart button ?>
											<div class="clear"></div>
											<span class="addtocart-button">
											<i>&gt;</i>
												<input type="submit" name="<?php echo $button_name ?>"  class="<?php echo $button_cls ?>" value="<?php echo $button_lbl ?>" title="<?php echo $button_lbl ?>" />
											</span>
											<?php if ($products_per_row <=3){ ?>
											<div class="det">
												<?php // Product Details Button
												echo JHTML::link($product->link, JText::_('TM_DETAILS').'<span>&gt;</span>', array('title' => $product->product_name,'class' => 'button'));
												?>
											</div>
											<?php }?>
											<div class="clear"></div>
											<?php }?>
											</div>
											<?php }?>
										
										</div>
										<input type="hidden" class="pname" value="<?php echo $product->product_name ?>"/>
										<input type="hidden" name="option" value="com_virtuemart" />
										<input type="hidden" name="view" value="cart" />
										<noscript><input type="hidden" name="task" value="add" /></noscript>
										<input type="hidden" name="virtuemart_product_id[]" value="<?php echo $product->virtuemart_product_id ?>" />
										<input type="hidden" name="virtuemart_category_id[]" value="<?php echo $product->virtuemart_category_id ?>" />
										
									</form>
						</div>
					<?php }?>
						</div>
					
					

					
			</div>
		</div>
	<?php
	$nb ++;

	// Do we need to close the current row now?
	if ($col == $products_per_row) { ?>
	<div class="clear"></div>
	</div>
		<?php
		$col = 1;
	} else {
		$col ++;
	}
}
// Do we need a final closing row tag?
if ($col != 1) { ?>
	<div class="clear"></div>
	</div>
<?php
}
?>
</div>
</div>
<?php } ?>


